<?php

declare(strict_types=1);

namespace HybridUpdater\Domain\Enumeration;

enum IncompatibilityReason: string
{
    case IncompatibleNative = 'incompatible_native';
}
