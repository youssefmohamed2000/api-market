<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    use HandlesAuthorization;

    public function view(User $user , Product $product)
    {
        return $user->id === $product->seller_id
            ? Response::allow()
            : Response::deny('You Are Not Allowed To Access This');
    }

    public function update(User $user, Product $product)
    {
        return $user->id === $product->seller_id
            ? Response::allow()
            : Response::deny('You Are Not Allowed To Access This');
    }

    public function delete(User $user, Product $product)
    {
        return $user->id === $product->seller_id
            ? Response::allow()
            : Response::deny('You Are Not Allowed To Access This');
    }

}
