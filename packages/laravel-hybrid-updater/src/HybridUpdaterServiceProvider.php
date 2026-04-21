<?php

declare(strict_types=1);

namespace HybridUpdater;

use HybridUpdater\Application\Port\AndroidReleaseReadRepository;
use HybridUpdater\Application\Port\WebBundleReadRepository;
use HybridUpdater\Application\UseCase\CheckUpdatesUseCase;
use HybridUpdater\Application\UseCase\StoreAndroidReleaseUseCase;
use HybridUpdater\Application\UseCase\StoreWebBundleUseCase;
use HybridUpdater\Http\Middleware\ValidateUploadToken;
use HybridUpdater\Infrastructure\Cache\CachedAndroidReleaseReadRepository;
use HybridUpdater\Infrastructure\Cache\CachedWebBundleReadRepository;
use HybridUpdater\Infrastructure\Persistence\EloquentAndroidReleaseReadRepository;
use HybridUpdater\Infrastructure\Persistence\EloquentWebBundleReadRepository;
use HybridUpdater\Infrastructure\Validation\ZipContainsIndexHtmlValidator;
use HybridUpdater\Infrastructure\Support\UpdateCacheInvalidator;
use HybridUpdater\Domain\Service\ManifestComposer;
use HybridUpdater\Domain\Service\PlatformInclusionPolicy;
use HybridUpdater\Domain\Service\UpdateDecisionEngine;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\ServiceProvider;

final class HybridUpdaterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/hybrid-updater.php', 'hybrid-updater');

        $this->app->singleton(ManifestComposer::class);
        $this->app->singleton(UpdateDecisionEngine::class);
        $this->app->singleton(PlatformInclusionPolicy::class);
        $this->app->singleton(ZipContainsIndexHtmlValidator::class);

        $this->app->singleton(UpdateCacheInvalidator::class);
        $this->app->singleton(CheckUpdatesUseCase::class);
        $this->app->singleton(StoreWebBundleUseCase::class);
        $this->app->singleton(StoreAndroidReleaseUseCase::class);

        $this->app->bind(WebBundleReadRepository::class, function ($app): WebBundleReadRepository {
            $ttl = (int) config('hybrid-updater.cache.app_version_ttl', 300);

            return new CachedWebBundleReadRepository(
                $app->make(EloquentWebBundleReadRepository::class),
                $app->make(CacheRepository::class),
                $ttl,
            );
        });

        $this->app->bind(AndroidReleaseReadRepository::class, function ($app): AndroidReleaseReadRepository {
            $ttl = (int) config('hybrid-updater.cache.app_version_ttl', 300);

            return new CachedAndroidReleaseReadRepository(
                $app->make(EloquentAndroidReleaseReadRepository::class),
                $app->make(CacheRepository::class),
                $ttl,
            );
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/hybrid-updater.php' => config_path('hybrid-updater.php'),
        ], 'hybrid-updater-config');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'hybrid-updater-migrations');

        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        /** @var \Illuminate\Routing\Router $router */
        $router = $this->app['router'];
        $router->aliasMiddleware('hybrid-updater.upload-token', ValidateUploadToken::class);
    }
}
