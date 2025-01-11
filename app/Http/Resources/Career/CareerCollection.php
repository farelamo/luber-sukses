<?php

namespace App\Http\Resources\Career;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CareerCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'data' => $this->collection->transform(function($data){
                return [
                    'id' => $data->id,
                    'title' => $data->title,
                    'job_open' => $data->job_open,
                    'job_closed' => $data->job_closed,
                    'desc' => $data->desc,
                    'created_at' => $data->created_at,
                    'updated_at' => $data->updated_at,
                ];
            })
        ];
    }
}
