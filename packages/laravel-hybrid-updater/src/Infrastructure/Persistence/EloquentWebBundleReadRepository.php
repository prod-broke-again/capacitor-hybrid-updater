<?php

declare(strict_types=1);

namespace HybridUpdater\Infrastructure\Persistence;

use HybridUpdater\Application\Port\WebBundleReadRepository;
use HybridUpdater\Domain\Dto\WebBundleSnapshot;
use HybridUpdater\Domain\ValueObject\ChannelName;
use HybridUpdater\Models\WebBundle;

final class EloquentWebBundleReadRepository implements WebBundleReadRepository
{
    public function findLatestActive(ChannelName $channel): ?WebBundleSnapshot
    {
        $row = WebBundle::query()
            ->where('channel', $channel->toString())
            ->where('is_active', true)
            ->orderByDesc('id')
            ->first();

        if ($row === null) {
            return null;
        }

        return new WebBundleSnapshot(
            version: (string) $row->version,
            zipUrl: (string) $row->zip_url,
            minNativeVersion: (string) $row->min_native_version,
            minNativeBuild: (int) $row->min_native_build,
            forceReload: (bool) $row->force_reload,
            checksum: $row->checksum !== null ? (string) $row->checksum : null,
            channel: (string) $row->channel,
            releaseDateIso: $row->created_at?->toIso8601String() ?? now()->toIso8601String(),
        );
    }
}
