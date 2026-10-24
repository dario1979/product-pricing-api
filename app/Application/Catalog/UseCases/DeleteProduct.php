<?php

namespace App\Application\Catalog\UseCases;

use App\Domain\Catalog\Contracts\ProductRepository;
use App\Domain\Catalog\Exceptions\ProductNotFound;

final readonly class DeleteProduct
{
    public function __construct(
        private ProductRepository $products,
    ) {
    }

    public function execute(int $id): void
    {
        if ($this->products->findById($id) === null) {
            throw new ProductNotFound($id);
        }

        $this->products->delete($id);
    }
}