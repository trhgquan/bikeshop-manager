<?php

namespace App\Http\Resources;

use App\Http\Resources\BikeResource;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        return [
            'id' => $this->id,
            'brand_name' => $this->brand_name,
            'bikes' => BikeResource::collection($this->bikes)
        ];
    }
}
