<?php

declare(strict_types=1);

namespace HybridUpdater\Infrastructure\Validation;

use ZipArchive;

final class ZipContainsIndexHtmlValidator
{
    public function passes(string $zipPath): bool
    {
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::RDONLY) !== true) {
            return false;
        }

        $hasIndex = false;
        for ($index = 0; $index < $zip->numFiles; $index++) {
            $name = trim(str_replace('\\', '/', (string) $zip->getNameIndex($index)), './');
            if ($name === 'index.html') {
                $hasIndex = true;
                break;
            }
        }
        $zip->close();

        return $hasIndex;
    }
}
