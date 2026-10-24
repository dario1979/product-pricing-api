<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Contracts;

use App\Domain\Catalog\Entities\ProductPrice;

interface ProductPriceRepository
{
    /**
     * @return list<ProductPrice>
     */
    public function findByProductId(int $productId): array;

    public function existsForProductAndCurrency(
        int $productId,
        int $currencyId,
    ): bool;

    public function save(ProductPrice $price): ProductPrice;
}
