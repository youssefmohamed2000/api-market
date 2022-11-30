<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class TransactionPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Transaction $transaction)
    {
        return $user->id === $transaction->buyer_id || $user->id === $transaction->product->seller_id
            ? Response::allow()
            : Response::deny('You Are Not Allowed To Access This');
    }

}
