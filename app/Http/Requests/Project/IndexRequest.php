<?php

namespace App\Http\Requests\Project;

use App\Enum\Project\Status;
use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
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
            'search' => 'nullable|string|max:255',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0|gt:min_price',
            'sort_by' => 'nullable|in:created_at,title,min_price,max_price,deadline',
            'sort_order' => 'nullable|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
            'status' => 'nullable|in:' . implode(',', array_column(Status::cases(), 'value')),
            'unit_type' => 'nullable|in:house,apartment,villa',
            'location' => 'nullable|in:urban,suburban,rural',
            'skill' => 'nullable|in:construction,design,renovation',
        ];
    }
}
