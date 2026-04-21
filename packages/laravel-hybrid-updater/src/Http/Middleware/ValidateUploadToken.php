<?php

declare(strict_types=1);

namespace HybridUpdater\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ValidateUploadToken
{
    public function handle(Request $request, Closure $next, string $scope): Response
    {
        $configured = (string) config("hybrid-updater.upload_tokens.{$scope}", '');
        if ($configured === '') {
            return new JsonResponse(['message' => 'Upload token not configured'], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $bearerToken = (string) $request->bearerToken();
        $headerName = $scope === 'android' ? 'X-Android-Upload-Token' : 'X-Web-Bundle-Upload-Token';
        $headerToken = (string) $request->header($headerName, '');
        $providedToken = $bearerToken !== '' ? $bearerToken : $headerToken;

        if (! hash_equals($configured, $providedToken)) {
            return new JsonResponse(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
