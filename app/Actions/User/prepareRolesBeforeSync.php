<?php
namespace App\Actions\User;

class prepareRolesBeforeSync
{
    public function __invoke(array $data): array
    {
        $tmp = [];
        $validated = $data['roles'];
        foreach ($validated as $role) {
            $tmp[] = $role['id'];
        }
        return $tmp;
    }
}