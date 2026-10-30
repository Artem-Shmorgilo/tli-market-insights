<?php

namespace App\Http\Requests\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'league' => ['nullable', 'integer', 'exists:leagues,id'],
            'category' => ['nullable', 'integer', 'exists:categories,id'],
            'search' => ['nullable', 'string', 'max:255'],
        ];
    }
}
