<?php

namespace App\Http\Requests;

use App\Enum\Offer\OfferType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreOfferRequest extends FormRequest
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
            'project_id' => 'required|exists:projects,id',
            'price' => 'required|numeric|min:0',
            'deadline' => 'required|date',
            'start_date' => 'required|date|after:deadline',
            'description' => 'required|string',
            'type' => ['required', new Enum(OfferType::class)],
            'expire_date' => 'required|date|after:start_date',
        ];
    }
}
