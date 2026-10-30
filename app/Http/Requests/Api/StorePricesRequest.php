<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StorePricesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_id' => [
                'required',
                'integer',
                'exists:items,external_id',
            ],
            'league_id' => [
                'required',
                'integer',
                'exists:leagues,id',
            ],
            'timestamp' => [
                'required',
                'date',
            ],
            'prices' => [
                'required',
                'array',
                'min:1',
                'max:1000',
            ],
            'prices.*' => [
                'required',
                'numeric',
                'gt:0',
            ],
        ];
    }
}
