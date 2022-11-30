<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\UserResource;
use App\Models\Seller;
use Illuminate\Support\Facades\Gate;

class SellerController extends Controller
{
    public function index()
    {
        if (!Gate::allows('admin-action')) {
            abort(403, 'You Are Not Allowed To Access This');
        }
        $sellers = Seller::has('products')->get();
        if ($sellers->count() > 0) {
            return $this->sendResponse(UserResource::collection($sellers), 'All Sellers Sent');
        }
        return $this->sendError('There are no Sellers');
    }

    public function show($id)
    {
        $seller = Seller::has('products')->find($id);
        if (!$seller) {
            return $this->sendError('Seller Not exist');
        }
        $this->authorize('view', $seller);
        return $this->sendResponse(new UserResource($seller), 'Seller Sent');
    }

    public function buyers($id)
    {
        $seller = Seller::query()->find($id);
        if (!$seller) {
            return $this->sendError('Seller Not Exist');
        }
        $this->authorize('view', $seller);
        $buyers = $seller->products()
            ->whereHas('transactions')
            ->with('transactions.buyer')
            ->get()
            ->pluck('transactions')
            ->collapse()
            ->pluck('buyer')
            ->unique('id')
            ->values();
        return $this->sendResponse(UserResource::collection($buyers), 'Buyers Sent');
    }

    public function categories($id)
    {
        $seller = Seller::query()->find($id);
        if (!$seller) {
            return $this->sendError('Seller Not Exist');
        }
        $this->authorize('view', $seller);
        $categories = $seller->products()
            ->with('category')
            ->get()
            ->pluck('category')
            ->unique('id')
            ->values();
        return $this->sendResponse(CategoryResource::collection($categories), 'Categories Sent');
    }

    public function products($id)
    {
        $seller = Seller::query()->find($id);
        if (!$seller) {
            return $this->sendError('Seller Not Exist');
        }
        $this->authorize('view', $seller);
        $products = $seller->products;
        return $this->sendResponse(ProductResource::collection($products), 'Sellers Sent');
    }

    public function transactions($id)
    {
        $seller = Seller::query()->find($id);
        if (!$seller) {
            return $this->sendError('Seller Not Exist');
        }
        $this->authorize('view', $seller);
        $transactions = $seller->products()
            ->whereHas('transactions')
            ->with('transactions')
            ->get()
            ->pluck('transactions')
            ->collapse();
        return $this->sendResponse(TransactionResource::collection($transactions), 'Transactions Sent');
    }
}
