<?php

declare(strict_types=1);

namespace HybridUpdater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CheckUpdatesRequest extends FormRequest
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
            'channel' => 'sometimes|string|max:64',
            'platform' => 'nullable|string|in:web,android',
            'native_version' => 'nullable|string|max:50',
            'native_build' => 'nullable|integer|min:0',
            'web_version' => 'nullable|string|max:50',
        ];
    }
}
