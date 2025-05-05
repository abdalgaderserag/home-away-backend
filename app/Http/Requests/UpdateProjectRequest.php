<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enum\Project\{Status, UnitType, Location, Skill};
use Illuminate\Validation\Rules\Enum;

class UpdateProjectRequest extends FormRequest
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
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'unit_type' => ['nullable', new Enum(UnitType::class)],
            'space' => 'nullable|integer|min:1',
            'location' => 'nullable|exists:locations,city',
            'deadline' => 'nullable|date',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|gt:min_price',
            'resources' => 'nullable|boolean',
            'skill' => ['nullable', new Enum(Skill::class)],
            'attachment' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ];
    }
}
