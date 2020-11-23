<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ChecklistItems;

class ChecklistAttributes extends JsonResource
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
            'object_domain' => $this->object_domain,
            'object_id' => $this->object_id,
            'description' => $this->description,
            'is_completed' => $this->is_completed,
            'completed_at' => $this->completed_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
            'due' => $this->due,
            'urgency' => $this->urgency,
            // 'items' => $this->when($this->include === "items", ChecklistItems::collection($this->items->all()))
        ];
    }
}
