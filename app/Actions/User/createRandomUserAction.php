<?php

namespace App\Actions\User;

use App\Models\User;

class createRandomUserAction
{
    public function __invoke(): User
    {
        $users = User::factory(1)->create();

        return $users->first();
    }
}
