<?php

namespace App\Http\Controllers\Admin;

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

        $users = User::with(['roles'])->get();

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

        $user = User::with(['roles'])->find($user->id);

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
