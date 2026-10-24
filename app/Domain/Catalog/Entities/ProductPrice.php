<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Entities;

use App\Domain\Catalog\ValueObjects\Money;

final readonly class ProductPrice
{
    public function __construct(
        private ?int $id,
        private int $productId,
        private int $currencyId,
        private Money $price,
    ) {}

    public static function register(
        int $productId,
        int $currencyId,
        Money $price,
    ): self {
        return new self(
            id: null,
            productId: $productId,
            currencyId: $currencyId,
            price: $price,
        );
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function productId(): int
    {
        return $this->productId;
    }

    public function currencyId(): int
    {
        return $this->currencyId;
    }

    public function price(): Money
    {
        return $this->price;
    }
}
