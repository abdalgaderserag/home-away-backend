<?php

namespace App\Http\Requests;

use App\Enum\Offer\MilestoneStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateMilestoneRequest extends FormRequest
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
            'status' => ['required', new Enum(MilestoneStatus::class)],
            'deadline' => 'required|date|after:now',
            'delivery_date' => 'nullable|date|after:deadline',
            'attachments' => 'nullable|json',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string|max:1000',
        ];
    }
}
