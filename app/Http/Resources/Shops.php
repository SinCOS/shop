<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Shops extends Resource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->title,
            'logo' => $this->logo,
            'background_image' => $this->background_image,
            'created_at' => $this->created_at,
        ];
    }
}
