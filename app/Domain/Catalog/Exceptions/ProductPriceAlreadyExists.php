<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Exceptions;

use DomainException;

final class ProductPriceAlreadyExists extends DomainException
{
    public function __construct(
        int $productId,
        int $currencyId,
    ) {
        parent::__construct(sprintf(
            'Product %d already has a price for currency %d.',
            $productId,
            $currencyId
        ));
    }
}
