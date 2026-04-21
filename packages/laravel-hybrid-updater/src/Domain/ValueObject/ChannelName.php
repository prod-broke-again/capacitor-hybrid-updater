<?php

declare(strict_types=1);

namespace HybridUpdater\Domain\ValueObject;

use InvalidArgumentException;

final readonly class ChannelName
{
    private string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            throw new InvalidArgumentException('Channel must be non-empty.');
        }
        if (strlen($trimmed) > 64) {
            throw new InvalidArgumentException('Channel exceeds maximum length.');
        }
        $this->value = $trimmed;
    }

    public function toString(): string
    {
        return $this->value;
    }
}
