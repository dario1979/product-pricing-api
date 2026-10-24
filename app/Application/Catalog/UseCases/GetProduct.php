<?php

namespace App\Application\Catalog\UseCases;

use App\Domain\Catalog\Contracts\ProductRepository;
use App\Domain\Catalog\Entities\Product;
use App\Domain\Catalog\Exceptions\ProductNotFound;

final readonly class GetProduct
{
    public function __construct(
        private ProductRepository $products,
    ) {
    }

    public function execute(int $productId): Product
    {
        $product = $this->products->findById($productId);

        if ($product === null) {
            throw new ProductNotFound($productId);
        }

        return $product;
    }
}