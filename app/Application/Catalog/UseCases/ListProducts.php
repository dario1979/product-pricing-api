<?php

namespace App\Application\Catalog\UseCases;

use App\Domain\Catalog\Contracts\ProductRepository;

final readonly class ListProducts
{
    public function __construct(
        private ProductRepository $products,
    ) {
    }

    public function execute(): array
    {
        return $this->products->all();
    }
}