<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'data' => $this->collection->transform(function($data){
                return [
                    'id' => $data->id,
                    'title' => $data->title,
                    'subtitle' => $data->subtitle,
                    'slug' => $data->slug,
                    'image' => $data->image != "" ? env('APP_URL', 'https://api.luber-sukses.com') . Storage::url('images/product/' . $data->image) : null,
                    'desc' => $data->desc,
                    'category_id' => $data->category_id,
                    // 'categories' => $data->categories->map(function($cat){
                    //     return [
                    //         'id' => $cat->id,
                    //         'name' => $cat->name
                    //     ];
                    // }),
                    'created_at' => $data->created_at,
                    'updated_at' => $data->updated_at,
                ];
            })
        ];
    }
}
