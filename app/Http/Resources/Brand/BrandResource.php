<?php

namespace App\Http\Resources\Brand;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'data' => [
                'id' => $this->id,
                'title' => $this->title,
                'image' => $this->image != "" ? env('APP_URL', 'localhost:8000') . Storage::url('images/brand/' . $this->image) : null,
                'is_show' => $this->is_show,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ]
        ];
    }
}
