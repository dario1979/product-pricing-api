<?php

namespace Tests\Unit\Domain\Catalog;

use App\Domain\Catalog\ValueObjects\ProductName;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ProductNameTest extends TestCase
{
    public function test_it_trims_product_name(): void
    {
        $name = new ProductName('Keyboard');

        $this->assertSame(
            'Keyboard',
            $name->value()
        );
    }

    public function test_it_rejects_empty_product_name(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        new ProductName('   ');
    }
}
