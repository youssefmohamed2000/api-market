<?php

namespace App\Policies;

use App\Models\Seller;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class SellerPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Seller $seller)
    {
        return $user->id === $seller->id
            ? Response::allow()
            : Response::deny('You Are Not Allowed To Access This');
    }

}
