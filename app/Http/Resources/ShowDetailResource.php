<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'success' => true,
            'data' => [
                'showing' => [
                    'id' => $this->id,
                    'status' => $this->status,
                    'datetime' => $this->datetime,
                    'object' => $this->object,
                    'type' => $this->type,
                    'is_close' => $this->is_close,
                ]
            ]
        ];
    }
}
