<?php

declare(strict_types=1);

namespace HybridUpdater\Application\Dto;

use HybridUpdater\Domain\Dto\ClientVersionsSnapshot;
use HybridUpdater\Domain\ValueObject\ChannelName;

final readonly class CheckUpdatesInput
{
    public function __construct(
        public ChannelName $channel,
        public ?string $platform,
        public ClientVersionsSnapshot $client,
    ) {}
}
