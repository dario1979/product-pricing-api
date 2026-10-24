<?php

namespace App\Domain\Catalog\Contracts;

interface CurrencyRepository
{
    public function exists(int $id): bool;
}