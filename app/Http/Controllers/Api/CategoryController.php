<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\UserResource;
use App\Models\Category;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return $this->sendResponse(CategoryResource::collection($categories), 'Categories Sent');
    }

    public function store(StoreCategoryRequest $request)
    {
        if (!Gate::allows('admin-action')) {
            abort(403, 'You Are Not Allowed To Access This');
        }
        $validated = $request->safe();
        $category = Category::query()->create([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);
        return $this->sendResponse(new CategoryResource($category), 'Category Created');
    }

    public function show($id)
    {
        $category = Category::query()->find($id);
        if (!$category) {
            return $this->sendError('Category not exist');
        }
        return $this->sendResponse(new CategoryResource($category), 'Category Sent');
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        if (!Gate::allows('admin-action')) {
            abort(403, 'You Are Not Allowed To Access This');
        }
        $category = Category::query()->find($id);
        if (!$category) {
            return $this->sendError('Category not exist');
        }
        $validated = $request->safe();
        $category->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);
        return $this->sendResponse(new CategoryResource($category), 'Category Updated');
    }

    public function destroy($id)
    {
        if (!Gate::allows('admin-action')) {
            abort(403, 'You Are Not Allowed To Access This');
        }
        $category = Category::query()->find($id);
        if (!$category) {
            return $this->sendError('Category not exist');
        }
        $category->delete();
        return $this->sendResponse(new CategoryResource($category), 'Category Deleted');
    }

    public function buyers($id)
    {
        if (!Gate::allows('admin-action')) {
            abort(403, 'You Are Not Allowed To Access This');
        }
        $category = Category::query()->find($id);
        if (!$category) {
            return $this->sendError('Category Not Exist');
        }
        $buyers = $category->products()
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

    public function products($id)
    {
        $category = Category::query()->find($id);
        if (!$category) {
            return $this->sendError('Category Not Exist');
        }
        $products = $category->products;
        return $this->sendResponse(ProductResource::collection($products), 'Products Sent');
    }

    public function sellers($id)
    {
        if (!Gate::allows('admin-action')) {
            abort(403, 'You Are Not Allowed To Access This');
        }
        $category = Category::query()->find($id);
        if (!$category) {
            return $this->sendError('Category Not Exist');
        }
        $sellers = $category->products()
            ->with('seller')
            ->get()
            ->pluck('seller')
            ->unique()
            ->values();
        return $this->sendResponse(UserResource::collection($sellers), 'Sellers Sent');
    }

    public function transactions($id)
    {
        if (!Gate::allows('admin-action')) {
            abort(403, 'You Are Not Allowed To Access This');
        }
        $category = Category::query()->find($id);
        if (!$category) {
            return $this->sendError('Category Not Exist');
        }
        $transactions = $category->products()
            ->whereHas('transactions')
            ->with('transactions')
            ->get()
            ->pluck('transactions')
            ->collapse();
        return $this->sendResponse(TransactionResource::collection($transactions), 'Transactions Sent');
    }
}
