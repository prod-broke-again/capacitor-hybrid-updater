<?php

declare(strict_types=1);

namespace HybridUpdater\Http\Controllers;

use HybridUpdater\Application\Dto\CheckUpdatesInput;
use HybridUpdater\Application\UseCase\CheckUpdatesUseCase;
use HybridUpdater\Domain\Dto\ClientVersionsSnapshot;
use HybridUpdater\Domain\ValueObject\ChannelName;
use HybridUpdater\Http\Requests\CheckUpdatesRequest;
use Illuminate\Http\JsonResponse;

final class CheckUpdatesController
{
    public function __construct(
        private readonly CheckUpdatesUseCase $checkUpdates,
    ) {}

    public function __invoke(CheckUpdatesRequest $request): JsonResponse
    {
        $channel = (string) $request->query('channel', 'stable');
        $platform = $request->query('platform');
        $platform = is_string($platform) ? $platform : null;

        $input = new CheckUpdatesInput(
            channel: new ChannelName($channel),
            platform: $platform,
            client: ClientVersionsSnapshot::fromNullable(
                nativeVersion: $request->query('native_version') !== null ? (string) $request->query('native_version') : null,
                nativeBuild: $request->query('native_build') !== null ? (int) $request->query('native_build') : null,
                webVersion: $request->query('web_version') !== null ? (string) $request->query('web_version') : null,
            ),
        );

        return response()->json($this->checkUpdates->execute($input)->toArray());
    }
}
