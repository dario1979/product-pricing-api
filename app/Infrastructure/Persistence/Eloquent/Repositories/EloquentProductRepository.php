<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Catalog\Contracts\ProductRepository;
use App\Domain\Catalog\Entities\Product as DomainProduct;
use App\Domain\Catalog\ValueObjects\Money;
use App\Domain\Catalog\ValueObjects\ProductName;
use App\Infrastructure\Persistence\Eloquent\Models\Currency;
use App\Infrastructure\Persistence\Eloquent\Models\Product;

final class EloquentProductRepository implements ProductRepository
{
    public function save(DomainProduct $product): DomainProduct
    {
        $model = Product::query()->create([
            'name' => $product->name()->value(),
            'description' => $product->description(),
            'price' => $product->price()->decimal(),
            'currency_id' => $product->currencyId(),
            'tax_cost' => $product->taxCost()->decimal(),
            'manufacturing_cost' => $product
                ->manufacturingCost()
                ->decimal(),
        ]);

        return $this->toDomain($model);
    }

    public function all(): array
    {
        return Product::query()
            ->orderBy('id')
            ->get()
            ->map(
                fn(Product $product): DomainProduct =>
                $this->toDomain($product)
            )
            ->all();
    }

    public function findById(int $id): ?DomainProduct
    {
        $model = Product::query()->find($id);

        if ($model === null) {
            return null;
        }

        return $this->toDomain($model);
    }

    public function update(
        int $id,
        DomainProduct $product,
    ): DomainProduct {
        $model = Product::query()->findOrFail($id);

        $model->update([
            'name' => $product->name()->value(),
            'description' => $product->description(),
            'price' => $product->price()->decimal(),
            'currency_id' => $product->currencyId(),
            'tax_cost' => $product->taxCost()->decimal(),
            'manufacturing_cost' => $product
                ->manufacturingCost()
                ->decimal(),
        ]);

        return $this->toDomain(
            $model->refresh()
        );
    }

    public function delete(int $id): void
    {
        Product::query()
            ->whereKey($id)
            ->delete();
    }

    private function toDomain(Product $model): DomainProduct
    {
        return new DomainProduct(
            id: $model->id,
            name: new ProductName($model->name),
            description: $model->description,
            price: Money::fromDecimal($model->price),
            currencyId: $model->currency_id,
            taxCost: Money::fromDecimal($model->tax_cost),
            manufacturingCost: Money::fromDecimal(
                $model->manufacturing_cost
            ),
        );
    }
}
