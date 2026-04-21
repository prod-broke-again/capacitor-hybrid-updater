<?php

declare(strict_types=1);

namespace HybridUpdater\Infrastructure\Persistence;

use HybridUpdater\Application\Port\AndroidReleaseReadRepository;
use HybridUpdater\Domain\Dto\AndroidReleaseSnapshot;
use HybridUpdater\Domain\ValueObject\ChannelName;
use HybridUpdater\Models\AndroidRelease;

final class EloquentAndroidReleaseReadRepository implements AndroidReleaseReadRepository
{
    public function findLatestActive(ChannelName $channel): ?AndroidReleaseSnapshot
    {
        $row = AndroidRelease::query()
            ->where('channel', $channel->toString())
            ->where('is_active', true)
            ->orderByDesc('build_number')
            ->first();

        if ($row === null) {
            return null;
        }

        return new AndroidReleaseSnapshot(
            version: (string) $row->version,
            buildNumber: (int) $row->build_number,
            downloadUrl: (string) $row->download_url,
            releaseNotes: $row->release_notes !== null ? (string) $row->release_notes : null,
            forceUpdate: (bool) $row->force_update,
            checksum: $row->checksum !== null ? (string) $row->checksum : null,
            channel: (string) $row->channel,
            releaseDateIso: $row->created_at?->toIso8601String() ?? now()->toIso8601String(),
        );
    }
}
