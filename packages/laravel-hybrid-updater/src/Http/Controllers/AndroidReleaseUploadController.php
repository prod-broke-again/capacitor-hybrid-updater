<?php

declare(strict_types=1);

namespace HybridUpdater\Http\Controllers;

use HybridUpdater\Application\UseCase\StoreAndroidReleaseUseCase;
use HybridUpdater\Http\Requests\AndroidReleaseStoreRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

final class AndroidReleaseUploadController
{
    public function __construct(
        private readonly StoreAndroidReleaseUseCase $useCase,
    ) {}

    public function __invoke(AndroidReleaseStoreRequest $request): JsonResponse
    {
        $file = $request->file('apk') ?? $request->file('file');
        if ($file === null) {
            throw ValidationException::withMessages(['apk' => 'APK file is required.']);
        }

        $validated = $request->validated();
        $payload = $this->useCase->execute($file, [
            'version' => (string) $validated['version'],
            'build_number' => (int) $validated['build_number'],
            'channel' => (string) ($validated['channel'] ?? 'stable'),
            'release_notes' => $validated['release_notes'] ?? null,
            'force_update' => $validated['force_update'] ?? null,
        ]);

        return response()->json(['data' => $payload], Response::HTTP_CREATED);
    }
}
