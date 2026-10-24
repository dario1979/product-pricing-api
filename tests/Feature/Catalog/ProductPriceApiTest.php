<?php

declare(strict_types=1);

namespace Tests\Feature\Catalog;

use App\Infrastructure\Persistence\Eloquent\Models\Currency;
use App\Infrastructure\Persistence\Eloquent\Models\Product;
use App\Infrastructure\Persistence\Eloquent\Models\ProductPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ProductPriceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_price_can_be_registered(): void
    {
        $usd = $this->createCurrency('US Dollar', 'USD');
        $eur = $this->createCurrency('Euro', 'EUR');

        $product = $this->createProduct($usd);

        $response = $this->postJson(
            "/products/{$product->id}/prices",
            [
                'currency_id' => $eur->id,
                'price' => '110.40',
            ]
        );

        $response
            ->assertCreated()
            ->assertJsonPath('data.product_id', $product->id)
            ->assertJsonPath('data.currency_id', $eur->id)
            ->assertJsonPath('data.price', '110.40');

        $this->assertDatabaseHas('product_prices', [
            'product_id' => $product->id,
            'currency_id' => $eur->id,
            'price' => '110.40',
        ]);
    }

    public function test_product_prices_can_be_listed(): void
    {
        $usd = $this->createCurrency('US Dollar', 'USD');
        $eur = $this->createCurrency('Euro', 'EUR');
        $ars = $this->createCurrency('Argentine Peso', 'ARS');

        $product = $this->createProduct($usd);

        ProductPrice::query()->create([
            'product_id' => $product->id,
            'currency_id' => $eur->id,
            'price' => '110.40',
        ]);

        ProductPrice::query()->create([
            'product_id' => $product->id,
            'currency_id' => $ars->id,
            'price' => '120000.00',
        ]);

        $response = $this->getJson(
            "/products/{$product->id}/prices"
        );

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.currency_id', $eur->id)
            ->assertJsonPath('data.0.price', '110.40')
            ->assertJsonPath('data.1.currency_id', $ars->id)
            ->assertJsonPath('data.1.price', '120000.00');
    }

    public function test_prices_list_is_empty_when_product_has_no_prices(): void
    {
        $usd = $this->createCurrency('US Dollar', 'USD');
        $product = $this->createProduct($usd);

        $this->getJson("/products/{$product->id}/prices")
            ->assertOk()
            ->assertExactJson([
                'data' => [],
            ]);
    }

    public function test_prices_from_other_products_are_not_returned(): void
    {
        $usd = $this->createCurrency('US Dollar', 'USD');
        $eur = $this->createCurrency('Euro', 'EUR');

        $firstProduct = $this->createProduct($usd, 'Keyboard');
        $secondProduct = $this->createProduct($usd, 'Mouse');

        ProductPrice::query()->create([
            'product_id' => $firstProduct->id,
            'currency_id' => $eur->id,
            'price' => '110.40',
        ]);

        ProductPrice::query()->create([
            'product_id' => $secondProduct->id,
            'currency_id' => $eur->id,
            'price' => '45.00',
        ]);

        $this->getJson("/products/{$firstProduct->id}/prices")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.price', '110.40');
    }

    public function test_returns_404_when_listing_prices_of_missing_product(): void
    {
        $this->getJson('/products/999999/prices')
            ->assertNotFound();
    }

    public function test_returns_404_when_registering_price_for_missing_product(): void
    {
        $eur = $this->createCurrency('Euro', 'EUR');

        $this->postJson('/products/999999/prices', [
            'currency_id' => $eur->id,
            'price' => '110.40',
        ])->assertNotFound();

        $this->assertDatabaseCount('product_prices', 0);
    }

    public function test_invalid_price_data_is_rejected(): void
    {
        $usd = $this->createCurrency('US Dollar', 'USD');
        $product = $this->createProduct($usd);

        $response = $this->postJson(
            "/products/{$product->id}/prices",
            [
                'currency_id' => 999999,
                'price' => '-15.00',
            ]
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'currency_id',
                'price',
            ]);

        $this->assertDatabaseCount('product_prices', 0);
    }

    public function test_duplicate_price_for_same_currency_is_rejected(): void
    {
        $usd = $this->createCurrency('US Dollar', 'USD');
        $eur = $this->createCurrency('Euro', 'EUR');

        $product = $this->createProduct($usd);

        ProductPrice::query()->create([
            'product_id' => $product->id,
            'currency_id' => $eur->id,
            'price' => '110.40',
        ]);

        $response = $this->postJson(
            "/products/{$product->id}/prices",
            [
                'currency_id' => $eur->id,
                'price' => '115.00',
            ]
        );

        $response
            ->assertStatus(409)
            ->assertJsonPath(
                'message',
                "Product {$product->id} already has a price for currency {$eur->id}."
            );

        $this->assertDatabaseCount('product_prices', 1);

        $this->assertDatabaseHas('product_prices', [
            'product_id' => $product->id,
            'currency_id' => $eur->id,
            'price' => '110.40',
        ]);
    }

    private function createCurrency(
        string $name,
        string $symbol,
    ): Currency {
        return Currency::query()->create([
            'name' => $name,
            'symbol' => $symbol,
            'exchange_rate' => '1.00000000',
        ]);
    }

    private function createProduct(
        Currency $currency,
        string $name = 'Mechanical Keyboard',
    ): Product {
        return Product::query()->create([
            'name' => $name,
            'description' => 'Product description',
            'price' => '120.00',
            'currency_id' => $currency->id,
            'tax_cost' => '12.00',
            'manufacturing_cost' => '50.00',
        ]);
    }
}

