<?php

namespace App\Http\Requests;

use App\Enum\Offer\MilestoneStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreMilestoneRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'deadline' => 'required|date|after:now',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string|max:1000',
        ];
    }
}
