<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductReportsResource extends JsonResource
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
            'title' => $this->title,
            'quantity'=> $this->quantity,
            'purchase_total'=> round($this->purchase_total,2),
            'sold_total'=> round($this->sold_total,2),
            'profit'=> $this->profit,
            'loss'=> $this->loss,
        ];
    }
}
