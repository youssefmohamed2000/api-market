<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category_id' => $this->category_id,
            'description' => $this->description,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'status' => $this->status,
            'image' => $this->image,
            'seller_id' => $this->seller_id
        ];
    }
}
