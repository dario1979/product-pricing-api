<?php

namespace Tests\Feature\Catalog;

use App\Infrastructure\Persistence\Eloquent\Models\Currency;
use App\Infrastructure\Persistence\Eloquent\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class UpdateProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_be_updated(): void
    {
        $usd = $this->createCurrency();

        $eur = Currency::query()->create([
            'name' => 'Euro',
            'symbol' => 'EUR',
            'exchange_rate' => '0.92000000',
        ]);

        $product = $this->createProduct($usd);

        $payload = [
            'name' => 'Updated Keyboard',
            'description' => 'Updated mechanical keyboard',
            'price' => '150.00',
            'currency_id' => $eur->id,
            'tax_cost' => '15.00',
            'manufacturing_cost' => '60.00',
        ];

        $response = $this->putJson(
            "/products/{$product->id}",
            $payload
        );

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $product->id)
            ->assertJsonPath('data.name', 'Updated Keyboard')
            ->assertJsonPath('data.price', '150.00')
            ->assertJsonPath('data.currency_id', $eur->id)
            ->assertJsonPath('data.tax_cost', '15.00')
            ->assertJsonPath('data.manufacturing_cost', '60.00');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Keyboard',
            'description' => 'Updated mechanical keyboard',
            'price' => '150.00',
            'currency_id' => $eur->id,
            'tax_cost' => '15.00',
            'manufacturing_cost' => '60.00',
        ]);

        $this->assertDatabaseCount('products', 1);
    }

    public function test_returns_404_when_product_does_not_exist(): void
    {
        $currency = $this->createCurrency();

        $payload = [
            'name' => 'Updated Keyboard',
            'description' => 'Updated description',
            'price' => '150.00',
            'currency_id' => $currency->id,
            'tax_cost' => '15.00',
            'manufacturing_cost' => '60.00',
        ];

        $response = $this->putJson(
            '/products/999999',
            $payload
        );

        $response
            ->assertNotFound()
            ->assertJsonPath(
                'message',
                'Product with ID 999999 does not exist.'
            );

        $this->assertDatabaseCount('products', 0);
    }

    public function test_invalid_data_does_not_update_product(): void
    {
        $currency = $this->createCurrency();
        $product = $this->createProduct($currency);

        $payload = [
            'name' => '',
            'description' => 'Invalid update',
            'price' => '-10.00',
            'currency_id' => 999999,
            'tax_cost' => '-5.00',
            'manufacturing_cost' => '-20.00',
        ];

        $response = $this->putJson(
            "/products/{$product->id}",
            $payload
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name',
                'price',
                'currency_id',
                'tax_cost',
                'manufacturing_cost',
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Mechanical Keyboard',
            'price' => '120.00',
            'currency_id' => $currency->id,
            'tax_cost' => '12.00',
            'manufacturing_cost' => '50.00',
        ]);
    }

    public function test_put_requires_all_mandatory_fields(): void
    {
        $currency = $this->createCurrency();
        $product = $this->createProduct($currency);

        $response = $this->putJson(
            "/products/{$product->id}",
            [
                'name' => 'Updated Keyboard',
            ]
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'price',
                'currency_id',
                'tax_cost',
                'manufacturing_cost',
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Mechanical Keyboard',
        ]);
    }

    private function createCurrency(): Currency
    {
        return Currency::query()->create([
            'name' => 'US Dollar',
            'symbol' => 'USD',
            'exchange_rate' => '1.00000000',
        ]);
    }

    private function createProduct(Currency $currency): Product
    {
        return Product::query()->create([
            'name' => 'Mechanical Keyboard',
            'description' => 'Mechanical keyboard',
            'price' => '120.00',
            'currency_id' => $currency->id,
            'tax_cost' => '12.00',
            'manufacturing_cost' => '50.00',
        ]);
    }
}

