<?php

namespace App\Http\Resources\Api\V1\Catalogue;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'attributes' => $this->attributes,
            'price' => $this->price_override ?? $this->product->price,
            'stock' => $this->stock,
        ];
    }
}
