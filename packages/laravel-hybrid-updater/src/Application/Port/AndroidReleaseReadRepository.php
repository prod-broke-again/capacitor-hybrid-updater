<?php

declare(strict_types=1);

namespace HybridUpdater\Application\Port;

use HybridUpdater\Domain\Dto\AndroidReleaseSnapshot;
use HybridUpdater\Domain\ValueObject\ChannelName;

interface AndroidReleaseReadRepository
{
    public function findLatestActive(ChannelName $channel): ?AndroidReleaseSnapshot;
}
