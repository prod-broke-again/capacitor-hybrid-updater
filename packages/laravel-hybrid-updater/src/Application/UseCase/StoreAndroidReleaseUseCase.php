<?php

declare(strict_types=1);

namespace HybridUpdater\Application\UseCase;

use HybridUpdater\Infrastructure\Support\UpdateCacheInvalidator;
use HybridUpdater\Models\AndroidRelease;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

final class StoreAndroidReleaseUseCase
{
    public function __construct(
        private readonly UpdateCacheInvalidator $invalidator,
    ) {}

    /**
     * @param array{version: string, build_number: int, channel: string, release_notes?: ?string, force_update?: bool} $meta
     *
     * @return array<string, mixed>
     */
    public function execute(UploadedFile $file, array $meta): array
    {
        $version = $meta['version'];
        $buildNumber = $meta['build_number'];
        $disk = (string) config('hybrid-updater.disks.android', 'android-releases');
        $path = $file->getRealPath();
        $checksum = is_string($path) && $path !== '' ? (hash_file('sha256', $path) ?: null) : null;
        $storedPath = $file->storeAs('', "app-{$version}-{$buildNumber}.apk", ['disk' => $disk]);
        $url = Storage::disk($disk)->url($storedPath);
        $channel = $meta['channel'];

        $release = AndroidRelease::query()->create([
            'version' => $version,
            'build_number' => $buildNumber,
            'download_url' => $url,
            'checksum' => $checksum,
            'channel' => $channel,
            'release_notes' => $meta['release_notes'] ?? null,
            'force_update' => (bool) ($meta['force_update'] ?? false),
            'is_active' => true,
        ]);

        $this->invalidator->forgetChannel($channel);

        return [
            'id' => $release->id,
            'version' => $release->version,
            'buildNumber' => $release->build_number,
            'downloadUrl' => $release->download_url,
            'checksum' => $release->checksum,
            'channel' => $release->channel,
            'releaseNotes' => $release->release_notes,
            'forceUpdate' => $release->force_update,
        ];
    }
}
