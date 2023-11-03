<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'       => $this->id,
            'status'   => $this->status,
            'datetime' => $this->datetime,
            'object'   => $this->object,
            'type'     => $this->type,
            'is_close' => $this->is_close,
        ];
    }
}
