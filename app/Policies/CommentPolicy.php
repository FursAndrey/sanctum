<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    // public function view(User $user, Comment $comment): bool
    // {
    //     return true;
    // }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return !is_null($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    // public function update(User $user, Comment $comment): bool
    // {
    //     //
    // }

    /**
     * Determine whether the user can delete the model.
     */
    // public function delete(User $user, Comment $comment): bool
    // {
    //     //
    // }

    /**
     * Determine whether the user can restore the model.
     */
    // public function restore(User $user, Comment $comment): bool
    // {
    //     //
    // }

    /**
     * Determine whether the user can permanently delete the model.
     */
    // public function forceDelete(User $user, Comment $comment): bool
    // {
    //     //
    // }
    
    public function createRandom(User $user): bool
    {
        $roles = $user->roles->pluck('title')->toArray();
        return in_array('Admin', $roles);
    }
}
