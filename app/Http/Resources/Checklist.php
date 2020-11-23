<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ChecklistAttributes;

class Checklist extends JsonResource
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
            'type' => !empty($this->type) ? $this->type : null,
            'id' => !empty($this->id) ? $this->id : null,
            'attributes' => !empty($this->attributes) ? new ChecklistAttributes($this->attributes) : null,
            'links' => [
                'self' => url('/checklist/'.$this->id),
            ],
        ];
    }
}
