<?php

declare(strict_types=1);

return [
    /** API: GET /check, POST /web-bundles, POST /android/releases */
    'route_prefix' => env('HYBRID_UPDATER_ROUTE_PREFIX', 'api/updater'),

    'cache' => [
        'app_version_ttl' => (int) env('HYBRID_UPDATER_APP_VERSION_TTL', 300),
    ],

    'upload_tokens' => [
        'android' => env('HYBRID_UPDATER_ANDROID_UPLOAD_TOKEN', ''),
        'web' => env('HYBRID_UPDATER_WEB_UPLOAD_TOKEN', ''),
    ],

    'disks' => [
        'android' => env('HYBRID_UPDATER_ANDROID_DISK', 'android-releases'),
        'web' => env('HYBRID_UPDATER_WEB_DISK', 'web-bundles'),
    ],
];
