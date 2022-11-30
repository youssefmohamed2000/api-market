<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\UserResource;
use App\Models\Buyer;
use Illuminate\Support\Facades\Gate;

class BuyerController extends Controller
{
    public function index()
    {
        if (!Gate::allows('admin-action')) {
            abort(403, 'You Are Not Allowed To Access This');
        }
        $buyers = Buyer::has('transactions')->get();
        if ($buyers->count() > 0) {
            return $this->sendResponse(UserResource::collection($buyers), 'Buyers Sent');
        }
        return $this->sendError('There are no buyers');
    }

    public function show($id)
    {
        $buyer = Buyer::has('transactions')->find($id);
        if (!$buyer) {
            return $this->sendError('Buyer Not exist');
        }
        $this->authorize('view', $buyer);
        return $this->sendResponse(new UserResource($buyer), 'Buyer Sent');
    }

    public function transactions($id)
    {
        $buyer = Buyer::query()->find($id);
        if (!$buyer) {
            return $this->sendError('Buyer Not Exist');
        }
        $this->authorize('view', $buyer);
        $transactions = $buyer->transactions;
        return $this->sendResponse(TransactionResource::collection($transactions), 'Transactions Sent');
    }

    public function categories($id)
    {
        $buyer = Buyer::query()->find($id);
        if (!$buyer) {
            return $this->sendError('Buyer Not Exist');
        }
        $this->authorize('view', $buyer);
        $categories = $buyer->transactions()
            ->with('product.category')
            ->get()
            ->pluck('product.category')
            ->unique('id')
            ->values(); //to remove empty item when repeated
        return $this->sendResponse(CategoryResource::collection($categories), 'Categories Sent');
    }

    public function products($id)
    {
        $buyer = Buyer::query()->find($id);
        if (!$buyer) {
            return $this->sendError('Buyer Not Exist');
        }
        $this->authorize('view', $buyer);
        $products = $buyer->transactions()
            ->with('product')
            ->get()
            ->pluck('product')
            ->unique('id')
            ->values();
        return $this->sendResponse(ProductResource::collection($products), 'Products Sent');
    }

    public function sellers($id)
    {
        $buyer = Buyer::query()->find($id);
        if (!$buyer) {
            return $this->sendError('Buyer Not Exist');
        }
        $this->authorize('view', $buyer);
        $sellers = $buyer->transactions()
            ->with('product.seller')
            ->get()
            ->pluck('product.seller')
            ->unique('id')
            ->values();
        return $this->sendResponse(UserResource::collection($sellers), 'Sellers Sent');
    }
}
