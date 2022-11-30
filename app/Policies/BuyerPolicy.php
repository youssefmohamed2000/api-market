<?php

namespace App\Policies;

use App\Models\Buyer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class BuyerPolicy
{
    use HandlesAuthorization;

    public function view(User $user , Buyer $buyer)
    {
        return $user->id === $buyer->id
            ? Response::allow()
            : Response::deny('You Are Not Allowed To Access This');
    }

    public function purchase(User $user, Buyer $buyer)
    {
        return $user->id === $buyer->id;
    }
}
