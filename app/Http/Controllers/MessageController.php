<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Models\Attachment;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use App\Notifications\MessageReceived;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $chats = Chat::with(['firstUser', 'secondUser', 'lastMessage'])
            ->where(function ($query) use ($userId) {
                $query->where('first_user_id', $userId)
                    ->orWhere('second_user_id', $userId);
            })
            ->orderByDesc(
                Message::select('created_at')
                    ->whereColumn('messages.chat_id', 'chats.id')
                    ->latest()
                    ->take(1)
            )
            ->paginate(10)
            ->through(function ($chat) use ($userId) {
                $otherUser = $chat->first_user_id === $userId
                    ? $chat->secondUser
                    : $chat->firstUser;

                return [
                    'id' => $chat->id,
                    'other_user' => $otherUser->only(['id', 'name', 'avatar']),
                    'last_message' => $chat->lastMessage?->only(['content', 'created_at'])
                ];
            });

        return response()->json([
            'chats' => $chats,
            'meta' => [
                'current_page' => $chats->currentPage(),
                'per_page' => $chats->perPage(),
                'total' => $chats->total(),
            ]
        ], Response::HTTP_OK);
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

        $chats = Chat::where(function ($query) use ($user) {
            $query->where('first_user_id', Auth::id())
                ->where('second_user_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('first_user_id', $user->id)
                ->where('second_user_id', Auth::id());
        });
        $chat_count = $chats->count();
        if ($chat_count === 0) {
            $data = [
                'first_user_id' => Auth::id(),
                'second_user_id' => $user->id,
            ];
            if (!empty($request->project_id)) {
                array_push($data, ['project_id', $request->project_id]);
            }
            $chat = Chat::create($data);
        } elseif ($chat_count === 1) {
            $temp = $chats->first();
            if (empty($temp->project_id) && !empty($request->project_id)) {
                $chat = Chat::create([
                    'project_id' => $request->project_id,
                    'first_user_id' => Auth::id(),
                    'second_user_id' => $user->id,
                ]);
            } else {
                $chat = Chat::create([
                    'first_user_id' => Auth::id(),
                    'second_user_id' => $user->id,
                ]);
            }
        } else {
            if (!empty($request->project_id))
                $chat = $chats->where("project_id", $request->project_id)->first();
            else
                $chat = $chats->where("project_id", '!=', $request->project_id)->first();
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

        $user->notify(new MessageReceived($message));

        $data = $message->load(['sender', 'receiver', 'attachments']);
        return response(["message" => $data], Response::HTTP_CREATED);
    }
}
