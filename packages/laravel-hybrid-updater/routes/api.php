<?php

declare(strict_types=1);

use HybridUpdater\Http\Controllers\AndroidReleaseUploadController;
use HybridUpdater\Http\Controllers\CheckUpdatesController;
use HybridUpdater\Http\Controllers\WebBundleUploadController;
use Illuminate\Support\Facades\Route;

Route::prefix(config('hybrid-updater.route_prefix', 'api/updater'))
    ->name('hybrid-updater.')
    ->group(function (): void {
        Route::get('/check', CheckUpdatesController::class)->name('check');
        Route::post('/web-bundles', WebBundleUploadController::class)
            ->middleware('hybrid-updater.upload-token:web')
            ->name('web.upload');
        Route::post('/android/releases', AndroidReleaseUploadController::class)
            ->middleware('hybrid-updater.upload-token:android')
            ->name('android.upload');
    });
