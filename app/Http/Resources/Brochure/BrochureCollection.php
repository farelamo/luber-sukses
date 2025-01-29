<?php

namespace App\Http\Resources\Brochure;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BrochureCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'data' => $this->collection->transform(function($data){
                return [
                    'id' => $data->id,
                    'title' => $data->title,
                    'file' => $data->file != "" ? env('APP_URL', 'localhost:8000') . Storage::url('public/pdf/brochure/' . $data->file) : null,
                    'is_choosen' => $data->is_choosen,
                    'created_at' => $data->created_at,
                    'updated_at' => $data->updated_at,
                ];
            })
        ];
    }
}
