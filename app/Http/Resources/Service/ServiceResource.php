<?php

namespace App\Http\Resources\Service;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
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
                'image' => $this->image != "" ? env('APP_URL', 'https://api.luber-sukses.com') . Storage::url('images/service/' . $this->image) : null,
                'desc' => $this->desc,
                // 'is_carousel' => $this->is_carousel,
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
