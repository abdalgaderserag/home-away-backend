<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
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

        return response()->json($chats);
    }

    public function show(Chat $chat): JsonResponse
    {
        $chat->load(['messages' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }, 'first_user', 'second_user']);

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

    public function store(StoreMessageRequest $request, User $user): JsonResponse
    {
        if ($user->id === Auth::id()) {
            return response()->json([
                'message' => 'Cannot send message to yourself'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $chat = Chat::firstOrCreateChat(Auth::id(), $user->id);

        $message = new Message($request->validated());
        $message->sender()->associate(Auth::user());
        $message->receiver()->associate($user);
        $message->chat()->associate($chat);
        $message->save();
        
        $chat->touch();

        return response()->json($message->load(['sender', 'receiver']), Response::HTTP_CREATED);
    }
}
