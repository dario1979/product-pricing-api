<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ProductPriceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id(),
            'product_id' => $this->resource->productId(),
            'currency_id' => $this->resource->currencyId(),
            'price' => $this->resource->price()->decimal(),
        ];
    }
}
