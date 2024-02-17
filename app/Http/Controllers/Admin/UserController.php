<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Ban\toggleBanChatAction;
use App\Actions\Ban\toggleBanCommentAction;
use App\Actions\Chat\deleteAllChatsWithAllMessagesAction;
use App\Actions\User\createRandomUserAction;
use App\Actions\User\prepareRolesBeforeSyncAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\CurrentUserForMenuResource;
use App\Http\Resources\User\UserExceptMeResource;
use App\Http\Resources\User\UserResource;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', User::class);

        $users = User::with(['roles', 'banChat', 'banComment'])->get();

        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     //
    // }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);

        $user = User::with(['roles', 'banChat', 'banComment'])->find($user->id);

        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $data = $request->validated();
        $preparedRoles = (new prepareRolesBeforeSyncAction())($data);
        $user->roles()->sync($preparedRoles);

        (new toggleBanChatAction())($user->id, $data['has_ban_chat']);
        unset($data['has_ban_chat']);

        (new toggleBanCommentAction())($user->id, $data['has_ban_comment']);
        unset($data['has_ban_comment']);

        if (isset($data['tg_name'])) {
            unset($data['roles']);
            $user->update($data);
        } else {
            $data['tg_name'] = null;
            $user->update($data);
        }

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->roles()->detach();

        //удаление всех чатов в которых был этот пользователь со всеми сообщениями
        if ($user->chats->count() != 0) {
            (new deleteAllChatsWithAllMessagesAction())($user);
        }

        if (! is_null($user->banChat) && $user->banChat->count() != 0) {
            $user->banChat->delete();
        }
        if (! is_null($user->banComment) && $user->banComment->count() != 0) {
            $user->banComment->delete();
        }

        $user->delete();

        return response()->noContent();
    }

    public function getCurrentUserForMenu()
    {
        return new CurrentUserForMenuResource(auth()->user());
    }

    public function storeRandomUser()
    {
        $this->authorize('create', User::class);
        $randomUser = (new createRandomUserAction())();

        return new UserResource($randomUser);
    }

    public function getUsersExceptMe()
    {
        $this->authorize('viewAnyExceptMe', User::class);

        $users = User::where('id', '!=', auth()->id())->get();

        return UserExceptMeResource::collection($users);
    }
}
