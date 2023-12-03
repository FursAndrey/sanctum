<?php

namespace App\Http\Controllers;

use App\Http\Requests\Chat\StoreRequest;
use App\Http\Resources\Chat\ChatResource;
use App\Models\Chat;
use Exception;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $data = $request->validated();

        $data['users'][] = auth()->id();
        sort($data['users']);
        $userIds = implode('-', $data['users']);

        try {
            DB::beginTransaction();

            $chat = Chat::firstOrCreate(
                [
                    'users' => $userIds,
                ],
                [
                    'users' => $userIds,
                    'title' => $data['title'],
                ]
            );

            $chat->users()->sync($data['users']);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }

        return ChatResource::make($chat)->resolve();
    }

    /**
     * Display the specified resource.
     */
    public function show(Chat $chat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, Chat $chat)
    // {
    //     //
    // }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(Chat $chat)
    // {
    //     //
    // }
}
