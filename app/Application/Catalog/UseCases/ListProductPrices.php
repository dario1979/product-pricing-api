<?php

declare(strict_types=1);

namespace App\Application\Catalog\UseCases;

use App\Domain\Catalog\Contracts\ProductPriceRepository;
use App\Domain\Catalog\Contracts\ProductRepository;
use App\Domain\Catalog\Exceptions\ProductNotFound;

final readonly class ListProductPrices
{
    public function __construct(
        private ProductRepository $products,
        private ProductPriceRepository $prices,
    ) {}

    public function execute(int $productId): array
    {
        if ($this->products->findById($productId) === null) {
            throw new ProductNotFound($productId);
        }

        return $this->prices->findByProductId($productId);
    }
}
