<?php

namespace App\Domain\Catalog\ValueObjects;

use InvalidArgumentException;

final readonly class Money
{
    private function __construct(
        private int $cents,
    ) {
        if ($cents < 0) {
            throw new InvalidArgumentException(
                'Money amount cannot be negative.'
            );
        }
    }

    public static function fromDecimal(string $amount): self
    {
        if (! preg_match('/^\d+(\.\d{1,2})?$/', $amount)) {
            throw new InvalidArgumentException(
                'Invalid monetary amount.'
            );
        }

        [$integer, $decimal] = array_pad(
            explode('.', $amount, 2),
            2,
            '00'
        );

        $decimal = str_pad($decimal, 2, '0');

        return new self(
            ((int) $integer * 100) + (int) $decimal
        );
    }

    public static function fromCents(int $cents): self
    {
        return new self($cents);
    }

    public function cents(): int
    {
        return $this->cents;
    }

    public function decimal(): string
    {
        return sprintf(
            '%d.%02d',
            intdiv($this->cents, 100),
            $this->cents % 100
        );
    }

    public function equals(self $other): bool
    {
        return $this->cents === $other->cents;
    }
}