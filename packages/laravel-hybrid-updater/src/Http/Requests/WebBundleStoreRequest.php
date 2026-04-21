<?php

declare(strict_types=1);

namespace HybridUpdater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class WebBundleStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'zip' => 'nullable|file|mimetypes:application/zip,application/x-zip-compressed|max:1048576',
            'file' => 'nullable|file|mimetypes:application/zip,application/x-zip-compressed|max:1048576',
            'version' => 'required|string|max:50',
            'channel' => 'nullable|string|max:50',
            'min_native_version' => 'nullable|string|max:50',
            'min_native_build' => 'nullable|integer|min:0',
            'force_reload' => 'nullable|boolean',
        ];
    }
}
