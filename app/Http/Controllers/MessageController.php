<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessageRequest;

class MessageController extends Controller
{
    public function index()
    {
        return Message::with(['sender', 'receiver'])->paginate();
    }

    public function store(StoreMessageRequest $request)
    {
        $message = Message::create($request->validated());
        return response()->json($message->load(['sender', 'receiver']), 201);
    }

    public function show(Message $message)
    {
        return $message->load(['sender', 'receiver']);
    }

    public function update(StoreMessageRequest $request, Message $message)
    {
        $message->update($request->validated());
        return response()->json($message);
    }

    public function destroy(Message $message)
    {
        $message->delete();
        return response()->noContent();
    }
}
