<?php

namespace Tests\Feature\Catalog;

use App\Infrastructure\Persistence\Eloquent\Models\Currency;
use App\Infrastructure\Persistence\Eloquent\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GetProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_can_be_listed(): void
    {
        $currency = Currency::query()->create([
            'name' => 'US Dollar',
            'symbol' => 'USD',
            'exchange_rate' => '1.00000000',
        ]);

        Product::query()->create([
            'name' => 'Keyboard',
            'description' => 'Mechanical keyboard',
            'price' => '120.00',
            'currency_id' => $currency->id,
            'tax_cost' => '12.00',
            'manufacturing_cost' => '50.00',
        ]);

        Product::query()->create([
            'name' => 'Mouse',
            'description' => 'Wireless mouse',
            'price' => '50.00',
            'currency_id' => $currency->id,
            'tax_cost' => '5.00',
            'manufacturing_cost' => '20.00',
        ]);

        $response = $this->getJson('/products');

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath(
                'data.0.name',
                'Keyboard'
            )
            ->assertJsonPath(
                'data.1.name',
                'Mouse'
            );
    }

    public function test_products_list_is_empty_when_no_products_exist(): void
    {
        $response = $this->getJson('/products');

        $response
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }
}
