<?php

declare(strict_types=1);

namespace HybridUpdater\Application\Port;

use HybridUpdater\Domain\Dto\WebBundleSnapshot;
use HybridUpdater\Domain\ValueObject\ChannelName;

interface WebBundleReadRepository
{
    public function findLatestActive(ChannelName $channel): ?WebBundleSnapshot;
}
