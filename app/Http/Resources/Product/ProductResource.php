<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'data' => [
                'id' => $this->id,
                'title' => $this->title,
                'subtitle' => $this->subtitle,
                'slug' => $this->slug,
                'image' => $this->image != "" ? env('APP_URL', 'localhost:8000') . Storage::url('public/images/product/' . $this->image) : null,
                'desc' => $this->desc,
                'category_id' => int($this->category_id),
                // 'categories' => $this->categories->map(function($cat){
                //     return [
                //         'id' => $cat->id,
                //         'name' => $cat->name
                //     ];
                // }),
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ]
        ];
    }
}
