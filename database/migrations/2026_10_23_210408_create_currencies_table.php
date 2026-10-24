<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table): void {
            $table->id();

            $table->string('name', 100);
            $table->string('symbol', 10);

            $table->decimal(
                'exchange_rate',
                total: 18,
                places: 8
            );

            $table->timestamps();

            $table->unique('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
