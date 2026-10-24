<?php

namespace Tests\Feature\Catalog;

use App\Infrastructure\Persistence\Eloquent\Models\Currency;
use App\Infrastructure\Persistence\Eloquent\Models\Product;
use App\Infrastructure\Persistence\Eloquent\Models\ProductPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class DeleteProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_be_deleted_with_its_prices(): void
    {
        $usd = $this->createCurrency();

        $eur = Currency::query()->create([
            'name' => 'Euro',
            'symbol' => 'EUR',
            'exchange_rate' => '0.92000000',
        ]);

        $product = $this->createProduct(
            $usd,
            'Mechanical Keyboard'
        );

        $price = ProductPrice::query()->create([
            'product_id' => $product->id,
            'currency_id' => $eur->id,
            'price' => '110.40',
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
        ]);

        $this->assertDatabaseHas('product_prices', [
            'id' => $price->id,
            'product_id' => $product->id,
        ]);

        $response = $this->deleteJson(
            "/products/{$product->id}"
        );

        $response->assertNoContent();

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);

        $this->assertDatabaseMissing('product_prices', [
            'id' => $price->id,
        ]);

        $this->assertDatabaseCount('products', 0);
        $this->assertDatabaseCount('product_prices', 0);

        // Las divisas no deben eliminarse.
        $this->assertDatabaseCount('currencies', 2);
    }

    public function test_returns_404_when_product_does_not_exist(): void
    {
        $response = $this->deleteJson(
            '/products/999999'
        );

        $response
            ->assertNotFound()
            ->assertJsonPath(
                'message',
                'Product with ID 999999 does not exist.'
            );

        $this->assertDatabaseCount('products', 0);
    }

    public function test_deleting_product_does_not_affect_other_products(): void
    {
        $currency = $this->createCurrency();

        $firstProduct = $this->createProduct(
            $currency,
            'Mechanical Keyboard'
        );

        $secondProduct = $this->createProduct(
            $currency,
            'Wireless Mouse'
        );

        $this->assertDatabaseCount('products', 2);

        $response = $this->deleteJson(
            "/products/{$firstProduct->id}"
        );

        $response->assertNoContent();

        $this->assertDatabaseMissing('products', [
            'id' => $firstProduct->id,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $secondProduct->id,
            'name' => 'Wireless Mouse',
        ]);

        $this->assertDatabaseCount('products', 1);
        $this->assertDatabaseCount('currencies', 1);
    }

    private function createCurrency(): Currency
    {
        return Currency::query()->create([
            'name' => 'US Dollar',
            'symbol' => 'USD',
            'exchange_rate' => '1.00000000',
        ]);
    }

    private function createProduct(
        Currency $currency,
        string $name,
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

