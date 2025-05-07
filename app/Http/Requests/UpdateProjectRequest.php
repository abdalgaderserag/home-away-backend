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
            'unit_type' => 'nullable|exists:unit_types,type',
            'space' => 'nullable|integer|min:1',
            'location' => 'nullable|exists:locations,city',
            'deadline' => 'nullable|date',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|gt:min_price',
            'resources' => 'nullable|boolean',
            'skill' => 'nullable|exists:skills,name',
            'attachment' => 'nullable|array|exists:attachments,id'
        ];
    }
}
