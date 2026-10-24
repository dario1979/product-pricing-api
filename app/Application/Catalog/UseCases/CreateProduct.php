<?php

namespace App\Application\Catalog\UseCases;

use App\Application\Catalog\DTOs\CreateProductData;
use App\Domain\Catalog\Contracts\CurrencyRepository;
use App\Domain\Catalog\Contracts\ProductRepository;
use App\Domain\Catalog\Entities\Product;
use App\Domain\Catalog\Exceptions\CurrencyNotFound;
use App\Domain\Catalog\ValueObjects\Money;
use App\Domain\Catalog\ValueObjects\ProductName;

final readonly class CreateProduct
{
    public function __construct(
        private ProductRepository $products,
        private CurrencyRepository $currencies,
    ) {
    }

    public function execute(CreateProductData $data): Product
    {
        if (! $this->currencies->exists($data->currencyId)) {
            throw new CurrencyNotFound($data->currencyId);
        }

        $product = Product::create(
            name: new ProductName($data->name),
            description: $data->description,
            price: Money::fromDecimal($data->price),
            currencyId: $data->currencyId,
            taxCost: Money::fromDecimal($data->taxCost),
            manufacturingCost: Money::fromDecimal(
                $data->manufacturingCost
            ),
        );

        return $this->products->save($product);
    }
}