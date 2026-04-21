<?php

declare(strict_types=1);

namespace HybridUpdater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class AndroidReleaseStoreRequest extends FormRequest
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
            'apk' => 'nullable|file|mimes:apk|max:2097152',
            'file' => 'nullable|file|mimes:apk|max:2097152',
            'version' => 'required|string|max:50',
            'build_number' => 'required|integer|min:1',
            'channel' => 'nullable|string|max:50',
            'release_notes' => 'nullable|string',
            'force_update' => 'nullable|boolean',
        ];
    }
}
