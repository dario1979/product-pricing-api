<?php

namespace Database\Seeders;

use App\Infrastructure\Persistence\Eloquent\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Currency::query()->updateOrCreate(
            ['name' => 'US Dollar'],
            [
                'symbol' => 'USD',
                'exchange_rate' => '1.00000000',
            ]
        );

        Currency::query()->updateOrCreate(
            ['name' => 'Euro'],
            [
                'symbol' => 'EUR',
                'exchange_rate' => '0.92000000',
            ]
        );

        Currency::query()->updateOrCreate(
            ['name' => 'Argentine Peso'],
            [
                'symbol' => 'ARS',
                'exchange_rate' => '1000.00000000',
            ]
        );
    }
}
