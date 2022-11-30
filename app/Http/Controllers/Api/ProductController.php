<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\UserResource;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return $this->sendResponse(ProductResource::collection($products), 'Products Sent');
    }

    public function store(StoreProductRequest $request)
    {
        $validated = $request->safe();
        $extension = $validated['image']->getClientOriginalExtension();
        $filename = time() . '.' . $extension;
        $validated['image']->move('assets/products', $filename);
        $product = Product::query()->create([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'quantity' => $validated['quantity'],
            'status' => $validated['status'] ?? 1,
            'image' => $filename,
            'seller_id' => auth()->user()->id
        ]);
        return $this->sendResponse(new ProductResource($product), 'Product Created');
    }

    public function show($id)
    {
        $product = Product::query()->find($id);
        if (!$product) {
            return $this->sendError('Product not exist');
        }
        return $this->sendResponse(new ProductResource($product), 'Product Sent');
    }

    public function update(UpdateProductRequest $request, $id)
    {
        $product = Product::query()->find($id);
        if (!$product) {
            return $this->sendError('Product Not Exist');
        }
        $this->authorize('update', $product);
        $validated = $request->safe();
        if (isset($validated['image'])) {
            $old_file = base_path('public\assets\products\\' . $product->image);
            if (file_exists($old_file)) {
                unlink($old_file);
            }
            $extension = $validated['image']->getClientOriginalExtension();
            $filename = time() . '.' . $extension;
            $validated['image']->move('assets/products', $filename);
        }
        $product->update([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'quantity' => $validated['quantity'],
            'status' => $validated['status'] ?? $product->status,
            'image' => $filename ?? $product->image,
        ]);
        return $this->sendResponse(new ProductResource($product), 'Product Updated');
    }

    public function destroy($id)
    {
        $product = Product::query()->find($id);
        if (!$product) {
            return $this->sendError('Product Not Exist');
        }
        $this->authorize('delete', $product);
        $file = base_path('public\assets\products\\' . $product->image);
        if (file_exists($file)) {
            unlink($file);
        }
        $product->delete();

        return $this->sendResponse(new ProductResource($product), 'Product Deleted');
    }

    public function buyers($id)
    {
        $product = Product::query()->find($id);
        if (!$product) {
            return $this->sendError('Product Not Exist');
        }
        $this->authorize('view', $product);
        $buyers = $product->transactions()
            ->with('buyer')
            ->get()
            ->pluck('buyer')
            ->unique('id')
            ->values();
        return $this->sendResponse(UserResource::collection($buyers), 'Buyers Sent');
    }

    public function category($id)
    {
        $product = Product::query()->find($id);
        if (!$product) {
            return $this->sendError('Product Not Exist');
        }
        $category = $product->category;
        return $this->sendResponse(new CategoryResource($category), 'Category Sent');
    }

    public function transactions($id)
    {
        $product = Product::query()->find($id);
        if (!$product) {
            return $this->sendError('Product Not Exist');
        }
        $this->authorize('view', $product);
        $transactions = $product->transactions;
        return $this->sendResponse(TransactionResource::collection($transactions), 'Transactions Sent');
    }

    public function seller($id)
    {
        $product = Product::query()->find($id);
        if (!$product) {
            return $this->sendError('Product Not Exist');
        }
        $seller = $product->seller;
        return $this->sendResponse(new UserResource($seller), 'Seller Sent');
    }
}
