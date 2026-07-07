<?php

namespace App\Http\Resources\Api\V1\Catalogue;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductImageResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'url' => $this->url(),
            'is_primary' => $this->is_primary,
            'position' => $this->position,
        ];
    }
}
