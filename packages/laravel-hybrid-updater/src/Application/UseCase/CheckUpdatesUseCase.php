<?php

declare(strict_types=1);

namespace HybridUpdater\Application\UseCase;

use HybridUpdater\Application\Dto\CheckUpdatesInput;
use HybridUpdater\Application\Dto\CheckUpdatesOutput;
use HybridUpdater\Application\Dto\CurrentClientEchoDto;
use HybridUpdater\Application\Dto\ResponseMetaDto;
use HybridUpdater\Application\Dto\UpdateDecisionDto;
use HybridUpdater\Application\Port\AndroidReleaseReadRepository;
use HybridUpdater\Application\Port\WebBundleReadRepository;
use HybridUpdater\Domain\Service\ManifestComposer;
use HybridUpdater\Domain\Service\PlatformInclusionPolicy;
use HybridUpdater\Domain\Service\UpdateDecisionEngine;

final class CheckUpdatesUseCase
{
    public function __construct(
        private readonly WebBundleReadRepository $webBundles,
        private readonly AndroidReleaseReadRepository $androidReleases,
        private readonly ManifestComposer $manifestComposer,
        private readonly UpdateDecisionEngine $decisionEngine,
        private readonly PlatformInclusionPolicy $platformPolicy,
    ) {}

    public function execute(CheckUpdatesInput $input): CheckUpdatesOutput
    {
        $channel = $input->channel->toString();
        $web = $this->platformPolicy->includeWeb($input->platform)
            ? $this->webBundles->findLatestActive($input->channel)
            : null;
        $native = $this->platformPolicy->includeNative($input->platform)
            ? $this->androidReleases->findLatestActive($input->channel)
            : null;

        $manifest = $this->manifestComposer->compose($web, $native, $channel);
        $decision = $this->decisionEngine->decide($manifest, $input->client);

        $decisionDto = new UpdateDecisionDto(
            hasUpdate: $manifest !== null && $decision->hasUpdate,
            updateType: $manifest?->updateType,
            isCompatible: $decision->isCompatible,
            reason: $decision->reason,
        );

        $meta = new ResponseMetaDto(
            channel: $channel,
            checkedAtIso: now()->toIso8601String(),
            source: 'laravel-hybrid-updater',
        );

        $current = new CurrentClientEchoDto(
            nativeVersion: $input->client->nativeVersion,
            nativeBuild: $input->client->nativeBuild,
            webVersion: $input->client->webVersion,
        );

        return new CheckUpdatesOutput(
            meta: $meta,
            current: $current,
            decision: $decisionDto,
            manifest: $manifest,
        );
    }
}
