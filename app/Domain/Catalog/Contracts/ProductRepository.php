<?php

namespace App\Domain\Catalog\Contracts;

use App\Domain\Catalog\Entities\Product;

interface ProductRepository
{
    public function save(Product $product): Product;

    public function all(): array;

    public function findById(int $id): ?Product;

    public function update(
        int $id,
        Product $product
    ): Product;

    public function delete(int $id): void;
}
