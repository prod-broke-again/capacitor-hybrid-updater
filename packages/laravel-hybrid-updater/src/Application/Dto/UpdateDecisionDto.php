<?php

declare(strict_types=1);

namespace HybridUpdater\Application\Dto;

use HybridUpdater\Domain\Enumeration\IncompatibilityReason;
use HybridUpdater\Domain\Enumeration\UpdateType;

final readonly class UpdateDecisionDto
{
    public function __construct(
        public bool $hasUpdate,
        public ?UpdateType $updateType,
        public bool $isCompatible,
        public ?IncompatibilityReason $reason,
    ) {}
}
