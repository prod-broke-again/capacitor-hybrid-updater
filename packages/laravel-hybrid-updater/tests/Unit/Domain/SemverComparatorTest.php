<?php

declare(strict_types=1);

namespace HybridUpdater\Tests\Unit\Domain;

use HybridUpdater\Domain\Service\SemverComparator;
use PHPUnit\Framework\TestCase;

final class SemverComparatorTest extends TestCase
{
    public function testGreaterPatch(): void
    {
        $c = new SemverComparator();
        self::assertSame(1, $c->compare('1.0.1', '1.0.0'));
    }

    public function testEqual(): void
    {
        $c = new SemverComparator();
        self::assertSame(0, $c->compare('2.0.0', '2.0.0'));
    }

    public function testIsGreaterOrEqual(): void
    {
        $c = new SemverComparator();
        self::assertTrue($c->isGreaterOrEqual('1.0.0', '0.9.9'));
    }
}
