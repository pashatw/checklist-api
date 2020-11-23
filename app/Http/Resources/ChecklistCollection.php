<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Checklist;

class ChecklistCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => Checklist::collection($this->collection),
        ];
    }

    public function withResponse($request, $response)
    {
        $data = $response->getData(true);
        $links = $data['links'];
        $meta = $data['meta'];

        $first = $data['links']['first'];
        $last = $data['links']['last'];
        $next = $data['links']['next'];
        $prev = $data['links']['prev'];

        $count = $data['meta']['to'];
        $total = $data['meta']['total'];

        $data['links'] = compact('first', 'last', 'next', 'prev');
        $data['meta'] = compact('count', 'total');
        $response->setData($data);
    }
}
