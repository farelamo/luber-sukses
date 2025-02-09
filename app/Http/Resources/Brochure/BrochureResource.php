<?php

namespace App\Http\Resources\Brochure;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class BrochureResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'data' => [
                'id' => $this->id,
                'title' => $this->title,
                'file' => $this->file != "" ? env('APP_URL', 'localhost:8000') . Storage::url('pdf/brochure/' . $this->file) : null,
                'is_choosen' => $this->is_choosen,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ]
        ];
    }
}
