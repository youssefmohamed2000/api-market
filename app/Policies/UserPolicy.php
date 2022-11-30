<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use HandlesAuthorization;

    public function view(User $auth, User $user)
    {
        return $auth->id === $user->id
            ? Response::allow()
            : Response::deny('You Are Not Allowed To Access This');
    }

    public function update(User $auth, User $user)
    {
        return $auth->id === $user->id
            ? Response::allow()
            : Response::deny('You Are Not Allowed To Access This');
    }

    public function delete(User $auth, User $user)
    {
        return $auth->id === $user->id
            ? Response::allow()
            : Response::deny('You Are Not Allowed To Access This');
    }

}
