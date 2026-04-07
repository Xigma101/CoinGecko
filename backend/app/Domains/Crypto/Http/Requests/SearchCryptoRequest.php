<?php

namespace App\Domains\Crypto\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Validates search query parameters for the cryptocurrency search endpoint.
 */
class SearchCryptoRequest extends FormRequest
{
    /**
     * Always return JSON validation errors (API-only backend).
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => $validator->errors()->first(),
        ], 422));
    }
    /**
     * Search is a public endpoint — no authorization required.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules for the search query.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'q' => ['required', 'string', 'min:2', 'max:100'],
        ];
    }

    /**
     * Custom error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'q.required' => 'A search query is required.',
            'q.min' => 'Search query must be at least 2 characters.',
        ];
    }
}
