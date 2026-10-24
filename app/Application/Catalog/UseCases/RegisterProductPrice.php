<?php

declare(strict_types=1);

namespace App\Application\Catalog\UseCases;

use App\Application\Catalog\DTOs\RegisterProductPriceData;
use App\Domain\Catalog\Contracts\CurrencyRepository;
use App\Domain\Catalog\Contracts\ProductPriceRepository;
use App\Domain\Catalog\Contracts\ProductRepository;
use App\Domain\Catalog\Entities\ProductPrice;
use App\Domain\Catalog\Exceptions\CurrencyNotFound;
use App\Domain\Catalog\Exceptions\ProductNotFound;
use App\Domain\Catalog\Exceptions\ProductPriceAlreadyExists;
use App\Domain\Catalog\ValueObjects\Money;

final readonly class RegisterProductPrice
{
    public function __construct(
        private ProductRepository $products,
        private CurrencyRepository $currencies,
        private ProductPriceRepository $prices,
    ) {}

    public function execute(
        int $productId,
        RegisterProductPriceData $data,
    ): ProductPrice {
        if ($this->products->findById($productId) === null) {
            throw new ProductNotFound($productId);
        }

        if (! $this->currencies->exists($data->currencyId)) {
            throw new CurrencyNotFound($data->currencyId);
        }

        if ($this->prices->existsForProductAndCurrency(
            $productId,
            $data->currencyId,
        )) {
            throw new ProductPriceAlreadyExists(
                $productId,
                $data->currencyId,
            );
        }

        $price = ProductPrice::register(
            productId: $productId,
            currencyId: $data->currencyId,
            price: Money::fromDecimal($data->price),
        );

        return $this->prices->save($price);
    }
}
