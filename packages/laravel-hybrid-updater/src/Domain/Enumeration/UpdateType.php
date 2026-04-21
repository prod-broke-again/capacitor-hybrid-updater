<?php

declare(strict_types=1);

namespace HybridUpdater\Domain\Enumeration;

enum UpdateType: string
{
    case OtaWebOnly = 'ota_web_only';
    case ApkRequired = 'apk_required';
    case ApkOrOta = 'apk_or_ota';
}
