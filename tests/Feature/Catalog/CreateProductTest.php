<?php

namespace Tests\Feature\Catalog;

use App\Infrastructure\Persistence\Eloquent\Models\Currency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_be_created(): void
    {
        $currency = Currency::query()->create([
            'name' => 'US Dollar',
            'symbol' => 'USD',
            'exchange_rate' => '1.00000000',
        ]);

        $response = $this->postJson('/products', [
            'name' => 'Mechanical Keyboard',
            'description' => 'Mechanical keyboard',
            'price' => '120.00',
            'currency_id' => $currency->id,
            'tax_cost' => '12.00',
            'manufacturing_cost' => '50.00',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath(
                'data.name',
                'Mechanical Keyboard'
            )
            ->assertJsonPath(
                'data.price',
                '120.00'
            )
            ->assertJsonPath(
                'data.currency_id',
                $currency->id
            );

        $this->assertDatabaseHas('products', [
            'name' => 'Mechanical Keyboard',
            'currency_id' => $currency->id,
            'price' => '120.00',
        ]);
    }

    public function test_product_creation_requires_valid_data(): void
    {
        $response = $this->postJson('/products', [
            'name' => '',
            'price' => '-10.00',
            'currency_id' => 999,
            'tax_cost' => '-1.00',
            'manufacturing_cost' => '-20.00',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name',
                'price',
                'currency_id',
                'tax_cost',
                'manufacturing_cost',
            ]);

        $this->assertDatabaseCount('products', 0);
    }
}
