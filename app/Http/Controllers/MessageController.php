<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Models\Attachment;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends Controller
{
    public function index(): JsonResponse
    {
        $userId = Auth::id();

        $chats = Chat::with(['firstUser', 'secondUser', 'lastMessage'])
            ->where(function ($query) use ($userId) {
                $query->where('first_user_id', $userId)
                    ->orWhere('second_user_id', $userId);
            })
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($chat) use ($userId) {
                $chat->other_user = $chat->first_user_id === $userId
                    ? $chat->second_user
                    : $chat->first_user;
                return $chat->only(['id', 'other_user', 'lastMessage']);
            });

        return response()->json(["chats" => $chats], Response::HTTP_OK);
    }

    public function show(Chat $chat): JsonResponse
    {
        $chat->load(['messages' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }, 'first_user', 'second_user', 'messages.attachments']);

        $otherUserId = $chat->first_user_id === Auth::id()
            ? $chat->second_user_id
            : $chat->first_user_id;

        return response()->json([
            'messages' => $chat->messages,
            'other_user' => $chat->first_user_id === Auth::id()
                ? $chat->second_user
                : $chat->first_user
        ]);
    }

    public function store(StoreMessageRequest $request, User $user)
    {
        if ($user->id === Auth::id()) {
            return response()->json([
                'message' => 'Cannot send message to yourself'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $chat = Chat::where(function ($query) use ($user) {
            $query->where('first_user_id', Auth::id())
                ->where('second_user_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('first_user_id', $user->id)
                ->where('second_user_id', Auth::id());
        })->first();
        if (!$chat) {
            $chat = Chat::create([
                'first_user_id' => Auth::id(),
                'second_user_id' => $user->id,
            ]);
        }




        $message = new Message($request->validated());
        $message->sender()->associate(Auth::user());
        $message->receiver()->associate($user);
        $message->chat()->associate($chat);
        $message->save();

        foreach ($request->attachments as $attach) {
            $attachment = Attachment::find($attach);
            $attachment->message_id = $message->id;
            $attachment->save();
        }

        $chat->touch();
        $data = $message->load(['sender', 'receiver', 'attachments']);
        return response(["message" => $data], Response::HTTP_CREATED);
    }
}
