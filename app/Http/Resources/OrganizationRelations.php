<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationRelations extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (empty($this->resource)) {
            return [];
        }
        return $this->resource->map(function ($item) {
            return [
                'org_name' => $item->name,
                'level' => 'item',
            ];
        });
    }
}
