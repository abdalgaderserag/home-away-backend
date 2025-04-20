<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enum\Project\{Status, UnitType, Location, Skill};
use Illuminate\Validation\Rules\Enum;

class StoreProjectRequest extends FormRequest
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
            'client_id' => 'required|exists:users,id',
            'designer_id' => 'nullable|exists:users,id',
            'status' => ['required', new Enum(Status::class)],
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'unit_type' => ['required', new Enum(UnitType::class)],
            'space' => 'required|integer|min:1',
            'location' => ['required', new Enum(Location::class)],
            'deadline' => 'required|date',
            'min_price' => 'required|numeric|min:0',
            'max_price' => 'required|numeric|gt:min_price',
            'resources' => 'boolean',
            'skill' => ['required', new Enum(Skill::class)],
            'attachments' => 'nullable|json',
        ];
    }
}
