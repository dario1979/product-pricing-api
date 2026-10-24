<?php

declare(strict_types=1);

namespace App\Application\Catalog\DTOs;

final readonly class RegisterProductPriceData
{
    public function __construct(
        public int $currencyId,
        public string $price,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            currencyId: (int) $data['currency_id'],
            price: (string) $data['price'],
        );
    }
}
