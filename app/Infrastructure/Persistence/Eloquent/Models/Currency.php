<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Currency extends Model
{
    protected $table = 'currencies';

    protected $fillable = [
        'name',
        'symbol',
        'exchange_rate',
    ];

    public function casts(): array
    {
        return [
            'exchange_rate' => 'decimal:8',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function productPrices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }
}