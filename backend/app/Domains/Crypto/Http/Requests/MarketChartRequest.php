<?php

namespace App\Domains\Crypto\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Validates query parameters for the market chart endpoint.
 */
class MarketChartRequest extends FormRequest
{
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => $validator->errors()->first(),
        ], 422));
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'currency' => ['sometimes', 'string', 'in:usd,eur,gbp,jpy,aud,cad,chf,cny,krw,inr'],
            'days'     => ['sometimes', 'string', 'in:1,7,30,90,365'],
        ];
    }

    public function messages(): array
    {
        return [
            'currency.in' => 'Unsupported currency.',
            'days.in'     => 'Days must be one of: 1, 7, 30, 90, 365.',
        ];
    }
}
