<?php

namespace App\Http\Controllers;

use App\Http\Requests\Message\StoreRequest;
use App\Http\Resources\Message\MessageResource;
use App\Jobs\StoreMessageStatusJob;
use App\Models\Chat;
use App\Models\Message;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Chat $chat)
    {
        $page = request('page') ?? 1;

        $messages = $chat->messages()
            ->with('user')
            ->orderBy('id', 'DESC')
            ->paginate(5, '*', 'page', $page);

        $lastPage = $messages->LastPage();

        $messages = MessageResource::collection($messages)->resolve();

        return response()->json([
            'messages' => $messages,
            'lastPage' => $lastPage,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request, Chat $chat)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        $message = Message::create([
            'user_id' => auth()->id(),
            'chat_id' => $data['chat_id'],
            'body' => $data['body'],
        ]);

        StoreMessageStatusJob::dispatchSync($message, $chat->users);

        return MessageResource::make($message)->resolve();
    }
}
