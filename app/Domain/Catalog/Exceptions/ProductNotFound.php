<?php

namespace App\Domain\Catalog\Exceptions;

use DomainException;

final class ProductNotFound extends DomainException
{
    public function __construct(int $productId)
    {
        parent::__construct(
            sprintf(
                'Product with ID %d does not exist.',
                $productId
            )
        );
    }
}