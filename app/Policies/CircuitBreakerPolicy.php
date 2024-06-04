<?php

namespace App\Policies;

use App\Models\CircuitBreaker;
use App\Models\User;

class CircuitBreakerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        $roles = $user->roles->pluck('title')->toArray();

        return in_array('Admin', $roles);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        $roles = $user->roles->pluck('title')->toArray();

        return in_array('Admin', $roles);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CircuitBreaker $circuit_breaker): bool
    {
        $roles = $user->roles->pluck('title')->toArray();

        return in_array('Admin', $roles);
    }
}
