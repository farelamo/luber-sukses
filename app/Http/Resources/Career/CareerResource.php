<?php

namespace App\Http\Resources\Career;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CareerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'data' => [
                'id' => $this->id,
                'title' => $this->title,
                'job_open' => $this->job_open,
                'job_closed' => $this->job_closed,
                'desc' => $this->desc,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ]
        ];
    }
}
