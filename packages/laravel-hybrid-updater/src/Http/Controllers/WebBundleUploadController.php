<?php

declare(strict_types=1);

namespace HybridUpdater\Http\Controllers;

use HybridUpdater\Application\UseCase\StoreWebBundleUseCase;
use HybridUpdater\Http\Requests\WebBundleStoreRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

final class WebBundleUploadController
{
    public function __construct(
        private readonly StoreWebBundleUseCase $useCase,
    ) {}

    public function __invoke(WebBundleStoreRequest $request): JsonResponse
    {
        $file = $request->file('zip') ?? $request->file('file');
        if ($file === null) {
            throw ValidationException::withMessages(['zip' => 'ZIP bundle is required.']);
        }

        $validated = $request->validated();
        $payload = $this->useCase->execute($file, [
            'version' => (string) $validated['version'],
            'channel' => (string) ($validated['channel'] ?? 'stable'),
            'min_native_version' => $validated['min_native_version'] ?? null,
            'min_native_build' => isset($validated['min_native_build']) ? (int) $validated['min_native_build'] : null,
            'force_reload' => $validated['force_reload'] ?? null,
        ]);

        return response()->json(['data' => $payload], Response::HTTP_CREATED);
    }
}
