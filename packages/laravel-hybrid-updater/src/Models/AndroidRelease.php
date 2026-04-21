<?php

declare(strict_types=1);

namespace HybridUpdater\Models;

use Illuminate\Database\Eloquent\Model;

final class AndroidRelease extends Model
{
    protected $table = 'android_releases';

    protected $fillable = [
        'version',
        'build_number',
        'download_url',
        'checksum',
        'channel',
        'release_notes',
        'force_update',
        'is_active',
    ];

    protected $casts = [
        'build_number' => 'integer',
        'force_update' => 'boolean',
        'is_active' => 'boolean',
    ];
}
