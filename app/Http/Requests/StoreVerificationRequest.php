<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVerificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'attachment' => ['required', 'array', 'size:3'],
            'attachment.*' => 'file|mimes:jpg,jpeg,png,pdf|max:10240',
        ];
    }

    public function messages()
    {
        return [
            'attachment.required' => 'You must upload 3 attachment.',
            'attachment.size' => 'Exactly 3 attachment are required.',
        ];
    }
}
