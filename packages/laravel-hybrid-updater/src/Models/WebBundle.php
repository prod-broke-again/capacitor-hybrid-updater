<?php

declare(strict_types=1);

namespace HybridUpdater\Models;

use Illuminate\Database\Eloquent\Model;

final class WebBundle extends Model
{
    protected $table = 'web_bundles';

    protected $fillable = [
        'version',
        'zip_url',
        'checksum',
        'channel',
        'min_native_version',
        'min_native_build',
        'force_reload',
        'is_active',
    ];

    protected $casts = [
        'min_native_build' => 'integer',
        'force_reload' => 'boolean',
        'is_active' => 'boolean',
    ];
}
