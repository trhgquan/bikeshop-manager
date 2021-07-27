<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BikeResource extends JsonResource
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
            'id' => $this->id,
            'bike_name' => $this->bike_name,
            'brand_id' => $this->brand->id,
            'brand_name' => $this->brand->brand_name
        ];
    }
}
