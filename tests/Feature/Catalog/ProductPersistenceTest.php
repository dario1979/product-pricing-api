<?php

namespace Tests\Feature\Catalog;

use App\Infrastructure\Persistence\Eloquent\Models\Currency;
use App\Infrastructure\Persistence\Eloquent\Models\Product;
use App\Infrastructure\Persistence\Eloquent\Models\ProductPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductPersistenceTest extends TestCase
{

    use RefreshDatabase;


    public function test_product_can_be_persisted_with_its_base_currency(): void
    {
        $currency = Currency::query()->create([
            'name' => 'US Dollar',
            'symbol' => 'USD',
            'exchange_rate' => '1.00000000',
        ]);

        $product = Product::query()->create([
            'name' => 'Mechanical Keyboard',
            'description' => 'Mechanical keyboard',
            'price' => '120.00',
            'currency_id' => $currency->id,
            'tax_cost' => '12.00',
            'manufacturing_cost' => '50.00',
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Mechanical Keyboard',
            'currency_id' => $currency->id,
        ]);

        $this->assertTrue(
            $product->currency->is($currency)
        );
    }

    public function test_product_can_have_prices_in_other_currencies(): void
    {
        $usd = Currency::query()->create([
            'name' => 'US Dollar',
            'symbol' => 'USD',
            'exchange_rate' => '1.00000000',
        ]);

        $eur = Currency::query()->create([
            'name' => 'Euro',
            'symbol' => 'EUR',
            'exchange_rate' => '0.92000000',
        ]);

        $product = Product::query()->create([
            'name' => 'Mechanical Keyboard',
            'description' => null,
            'price' => '120.00',
            'currency_id' => $usd->id,
            'tax_cost' => '12.00',
            'manufacturing_cost' => '50.00',
        ]);

        $price = ProductPrice::query()->create([
            'product_id' => $product->id,
            'currency_id' => $eur->id,
            'price' => '110.40',
        ]);

        $product->load('prices');

        $this->assertTrue(
            $price->product->is($product)
        );

        $this->assertTrue(
            $price->currency->is($eur)
        );

        $this->assertCount(
            1,
            $product->prices
        );
    }

    public function test_product_prices_are_deleted_when_product_is_deleted(): void
    {
        $usd = Currency::query()->create([
            'name' => 'US Dollar',
            'symbol' => 'USD',
            'exchange_rate' => '1.00000000',
        ]);

        $eur = Currency::query()->create([
            'name' => 'Euro',
            'symbol' => 'EUR',
            'exchange_rate' => '0.92000000',
        ]);

        $product = Product::query()->create([
            'name' => 'Mechanical Keyboard',
            'description' => null,
            'price' => '120.00',
            'currency_id' => $usd->id,
            'tax_cost' => '12.00',
            'manufacturing_cost' => '50.00',
        ]);

        ProductPrice::query()->create([
            'product_id' => $product->id,
            'currency_id' => $eur->id,
            'price' => '110.40',
        ]);

        $product->delete();

        $this->assertDatabaseCount(
            'product_prices',
            0
        );
    }
}
