<?php

namespace App\Domain\Catalog\Entities;

use App\Domain\Catalog\ValueObjects\Money;
use App\Domain\Catalog\ValueObjects\ProductName;

final readonly class Product
{
    public function __construct(
        private ?int $id,
        private ProductName $name,
        private ?string $description,
        private Money $price,
        private int $currencyId,
        private Money $taxCost,
        private Money $manufacturingCost,
    ) {
    }

    public static function create(
        ProductName $name,
        ?string $description,
        Money $price,
        int $currencyId,
        Money $taxCost,
        Money $manufacturingCost,
    ): self {
        return new self(
            id: null,
            name: $name,
            description: $description,
            price: $price,
            currencyId: $currencyId,
            taxCost: $taxCost,
            manufacturingCost: $manufacturingCost,
        );
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function name(): ProductName
    {
        return $this->name;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function price(): Money
    {
        return $this->price;
    }

    public function currencyId(): int
    {
        return $this->currencyId;
    }

    public function taxCost(): Money
    {
        return $this->taxCost;
    }

    public function manufacturingCost(): Money
    {
        return $this->manufacturingCost;
    }
}