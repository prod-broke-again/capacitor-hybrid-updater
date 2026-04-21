<?php

declare(strict_types=1);

namespace HybridUpdater\Application\UseCase;

use HybridUpdater\Infrastructure\Support\UpdateCacheInvalidator;
use HybridUpdater\Infrastructure\Validation\ZipContainsIndexHtmlValidator;
use HybridUpdater\Models\WebBundle;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

final class StoreWebBundleUseCase
{
    public function __construct(
        private readonly UpdateCacheInvalidator $invalidator,
        private readonly ZipContainsIndexHtmlValidator $zipValidator,
    ) {}

    /**
     * @param array{version: string, channel: string, min_native_version?: string, min_native_build?: int, force_reload?: bool} $meta
     *
     * @return array<string, mixed>
     */
    public function execute(UploadedFile $file, array $meta): array
    {
        $path = (string) $file->getRealPath();
        if ($path === '' || ! $this->zipValidator->passes($path)) {
            throw ValidationException::withMessages(['zip' => 'ZIP must contain index.html at root level.']);
        }

        $checksum = hash_file('sha256', $path) ?: null;

        $version = $meta['version'];
        $disk = (string) config('hybrid-updater.disks.web', 'web-bundles');
        $storedPath = $file->storeAs('', "{$version}.zip", ['disk' => $disk]);
        $url = Storage::disk($disk)->url($storedPath);
        $channel = $meta['channel'];

        $bundle = WebBundle::query()->create([
            'version' => $version,
            'zip_url' => $url,
            'checksum' => $checksum,
            'channel' => $channel,
            'min_native_version' => (string) ($meta['min_native_version'] ?? '0.0.0'),
            'min_native_build' => (int) ($meta['min_native_build'] ?? 0),
            'force_reload' => (bool) ($meta['force_reload'] ?? false),
            'is_active' => true,
        ]);

        $this->invalidator->forgetChannel($channel);

        return [
            'id' => $bundle->id,
            'version' => $bundle->version,
            'zipUrl' => $bundle->zip_url,
            'checksum' => $bundle->checksum,
            'channel' => $bundle->channel,
            'minNativeVersion' => $bundle->min_native_version,
            'minNativeBuild' => $bundle->min_native_build,
            'forceReload' => $bundle->force_reload,
        ];
    }
}
