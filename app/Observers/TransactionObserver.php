<?php

namespace App\Observers;

use App\Models\Transaction;

class TransactionObserver
{
    public function created(Transaction $transaction)
    {
        $new_quantity = $transaction->product->quantity - $transaction->quantity;
        $transaction->product()->update([
            'quantity' => $new_quantity,
            'status' => $new_quantity == 0 ? 0 : $transaction->product->status
        ]);
    }
}
