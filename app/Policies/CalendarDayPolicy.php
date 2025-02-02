<?php

namespace App\Policies;

use App\Models\CalendarDay;
use App\Models\User;

class CalendarDayPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CalendarDay $calendar): bool
    {
        return false;
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
     * Determine whether the user can update the model.
     */
    public function update(User $user, CalendarDay $calendar): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CalendarDay $calendar): bool
    {
        $roles = $user->roles->pluck('title')->toArray();

        return in_array('Admin', $roles);
    }
}
