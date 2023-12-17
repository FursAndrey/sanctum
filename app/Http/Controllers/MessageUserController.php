<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessageUser\UpdateRequest;
use App\Models\Chat;
use App\Models\MessageUser;

class MessageUserController extends Controller
{
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Chat $chat)
    {
        $data = $request->validated();

        MessageUser::where('message_id', '=', $data['message_id'])
            ->where('user_id', '=', auth()->user()->id)
            ->update(['is_read' => true]);

        return response()->noContent();
    }
}
