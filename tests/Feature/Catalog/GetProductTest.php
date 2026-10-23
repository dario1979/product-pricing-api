<?php

namespace Tests\Feature\Catalog;

use App\Infrastructure\Persistence\Eloquent\Models\Currency;
use App\Infrastructure\Persistence\Eloquent\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GetProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_be_retrieved_by_id(): void
    {
        $currency = Currency::query()->create([
            'name' => 'US Dollar',
            'symbol' => 'USD',
            'exchange_rate' => '1.00000000',
        ]);

        $product = Product::query()->create([
            'name' => 'Keyboard',
            'description' => 'Mechanical keyboard',
            'price' => '120.00',
            'currency_id' => $currency->id,
            'tax_cost' => '12.00',
            'manufacturing_cost' => '50.00',
        ]);

        $response = $this->getJson(
            "/products/{$product->id}"
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.id',
                $product->id
            )
            ->assertJsonPath(
                'data.name',
                'Keyboard'
            )
            ->assertJsonPath(
                'data.price',
                '120.00'
            );
    }

    public function test_returns_404_when_product_does_not_exist(): void
    {
        $response = $this->getJson('/products/999');

        $response
            ->assertNotFound()
            ->assertJson([
                'message' => 'Product with ID 999 does not exist.',
            ]);
    }
}