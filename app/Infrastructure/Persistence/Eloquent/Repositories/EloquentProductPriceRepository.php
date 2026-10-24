<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Catalog\Contracts\ProductPriceRepository;
use App\Domain\Catalog\Entities\ProductPrice as DomainProductPrice;
use App\Domain\Catalog\Exceptions\ProductPriceAlreadyExists;
use App\Domain\Catalog\ValueObjects\Money;
use App\Infrastructure\Persistence\Eloquent\Models\ProductPrice;
use Illuminate\Database\UniqueConstraintViolationException;

final class EloquentProductPriceRepository implements ProductPriceRepository
{
    public function findByProductId(int $productId): array
    {
        return ProductPrice::query()
            ->where('product_id', $productId)
            ->orderBy('id')
            ->get()
            ->map(
                fn (ProductPrice $model): DomainProductPrice =>
                    $this->toDomain($model)
            )
            ->all();
    }

    public function existsForProductAndCurrency(
        int $productId,
        int $currencyId,
    ): bool {
        return ProductPrice::query()
            ->where('product_id', $productId)
            ->where('currency_id', $currencyId)
            ->exists();
    }

    public function save(
        DomainProductPrice $price,
    ): DomainProductPrice {
        try {
            $model = ProductPrice::query()->create([
                'product_id' => $price->productId(),
                'currency_id' => $price->currencyId(),
                'price' => $price->price()->decimal(),
            ]);
        } catch (UniqueConstraintViolationException $exception) {
            throw new ProductPriceAlreadyExists(
                $price->productId(),
                $price->currencyId(),
            );
        }

        return $this->toDomain($model);
    }

    private function toDomain(
        ProductPrice $model,
    ): DomainProductPrice {
        return new DomainProductPrice(
            id: $model->id,
            productId: $model->product_id,
            currencyId: $model->currency_id,
            price: Money::fromDecimal($model->price),
        );
    }
}
