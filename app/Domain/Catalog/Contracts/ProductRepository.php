<?php

namespace App\Domain\Catalog\Contracts;

use App\Domain\Catalog\Entities\Product;

interface ProductRepository
{
    public function save(Product $product): Product;

    public function all(): array;

    public function findById(int $id): ?Product;
}