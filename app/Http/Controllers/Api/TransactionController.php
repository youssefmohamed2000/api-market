<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\UserResource;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\Gate;

class TransactionController extends Controller
{

    public function index()
    {
        if (!Gate::allows('admin-action')) {
            abort(403, 'You Are Not Allowed To Access This');
        }
        $transactions = Transaction::all();
        return $this->sendResponse(TransactionResource::collection($transactions), 'Transactions Sent');
    }

    public function store(StoreTransactionRequest $request, $id)
    {
        $product = Product::query()->find($id);
        if (!$product) {
            return $this->sendError('Product Not Exist');
        } elseif ($product->status == 0) {
            return $this->sendError('Product Not Available');
        }
        $validated = $request->safe();
        if ($validated['quantity'] > $product->quantity) {
            return $this->sendError('The product dosen\'t have enough items', null, 422);
        }
        $transaction = Transaction::query()->create([
            'quantity' => $validated['quantity'],
            'buyer_id' => auth()->user()->id,
            'product_id' => $id,
        ]);
        return $this->sendResponse(new TransactionResource($transaction), 'Transaction Created');
    }

    public function show($id)
    {
        $transaction = Transaction::query()->find($id);
        if (!$transaction) {
            return $this->sendError('Transactions not exist');
        }
        $this->authorize('view' , $transaction);
        return $this->sendResponse(new TransactionResource($transaction), 'Transactions Sent');
    }

    public function category($id)
    {
        $transaction = Transaction::query()->find($id);
        if (!$transaction) {
            return $this->sendError('Transaction Not exist');
        }
        $this->authorize('view' , $transaction);
        $category = $transaction->product->category;
        return $this->sendResponse(new CategoryResource($category), 'Category Sent');
    }

    public function seller($id)
    {
        $transaction = Transaction::query()->find($id);
        if (!$transaction) {
            return $this->sendError('Transaction Not exist');
        }
        $this->authorize('view' , $transaction);
        $seller = $transaction->product->seller;
        return $this->sendResponse(new UserResource($seller), 'Seller Sent');
    }

}
