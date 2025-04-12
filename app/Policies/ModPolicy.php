<?php

namespace App\Policies;

use App\Models\Mod;
use App\Models\User;

class ModPolicy
{
    public function manage(User $user, Mod $mod): bool
    {
        return $user && $user->id === $mod->user_id;
    }

    public function create(User $user): bool
    {
        return !!$user->id;
    }

    public function view(?User $user): bool
    {
        return true;
    }
}
