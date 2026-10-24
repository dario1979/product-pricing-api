<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Catalog\Contracts\CurrencyRepository;
use App\Infrastructure\Persistence\Eloquent\Models\Currency;

final class EloquentCurrencyRepository implements CurrencyRepository
{
    public function exists(int $id): bool
    {
        return Currency::query()
            ->whereKey($id)
            ->exists();
    }
}