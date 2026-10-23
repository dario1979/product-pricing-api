<?php

namespace Tests\Unit\Domain\Catalog;

use App\Domain\Catalog\ValueObjects\Money;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    public function test_it_creates_money_from_decimal_value(): void
    {
        $money = Money::fromDecimal('120.50');

        $this->assertSame(12050, $money->cents());
        $this->assertSame('120.50', $money->decimal());
    }

    public function test_it_rejects_invalid_money_values(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Money::fromDecimal('-10.00');
    }
}
