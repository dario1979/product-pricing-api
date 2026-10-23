<?php

namespace App\Domain\Catalog\Exceptions;

use DomainException;

final class CurrencyNotFound extends DomainException
{
    public function __construct(int $currencyId)
    {
        parent::__construct(
            sprintf('Currency with ID %d does not exist.', $currencyId)
        );
    }
}