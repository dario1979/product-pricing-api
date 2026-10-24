<?php

namespace App\Application\Catalog\DTOs;

final readonly class CreateProductData
{
    public function __construct(
        public string $name,
        public ?string $description,
        public string $price,
        public int $currencyId,
        public string $taxCost,
        public string $manufacturingCost,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            description: $data['description'] ?? null,
            price: (string) $data['price'],
            currencyId: (int) $data['currency_id'],
            taxCost: (string) $data['tax_cost'],
            manufacturingCost: (string) $data['manufacturing_cost'],
        );
    }
}