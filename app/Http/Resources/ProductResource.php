<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id(),

            'name' => $this->resource
                ->name()
                ->value(),

            'description' => $this->resource->description(),

            'price' => $this->resource
                ->price()
                ->decimal(),

            'currency_id' => $this->resource->currencyId(),

            'tax_cost' => $this->resource
                ->taxCost()
                ->decimal(),

            'manufacturing_cost' => $this->resource
                ->manufacturingCost()
                ->decimal(),
        ];
    }
}