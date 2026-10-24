<?php

namespace App\Domain\Catalog\ValueObjects;

use InvalidArgumentException;

final readonly class ProductName
{
    private const MAX_LENGTH = 150;

    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        if ($value === '') {
            throw new InvalidArgumentException(
                'Product name cannot be empty.'
            );
        }

        if (mb_strlen($value) > self::MAX_LENGTH) {
            throw new InvalidArgumentException(
                'Product name cannot exceed 150 characters.'
            );
        }

        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }
}