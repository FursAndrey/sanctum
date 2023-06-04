<?php

namespace App\Policies;

use App\Models\User;

class PreviewPolicy
{
    /**
     * Create a new policy instance.
     */
    public function store(User $user): bool
    {
        $roles = $user->roles->pluck('title')->toArray();
        return in_array('Admin', $roles);
    }
}
