<?php

declare(strict_types=1);

namespace HybridUpdater\Domain\Service;

/**
 * Dot-separated numeric semver comparison (KISS, matches client TS helper).
 */
final class SemverComparator
{
    public function compare(string $a, string $b): int
    {
        $pa = array_map(static fn (string $s): int => (int) $s, explode('.', $a));
        $pb = array_map(static fn (string $s): int => (int) $s, explode('.', $b));
        $max = max(count($pa), count($pb));

        for ($i = 0; $i < $max; $i++) {
            $left = $pa[$i] ?? 0;
            $right = $pb[$i] ?? 0;
            if ($left > $right) {
                return 1;
            }
            if ($left < $right) {
                return -1;
            }
        }

        return 0;
    }

    public function isGreater(string $left, string $right): bool
    {
        return $this->compare($left, $right) > 0;
    }

    public function isGreaterOrEqual(string $left, string $right): bool
    {
        return $this->compare($left, $right) >= 0;
    }
}
