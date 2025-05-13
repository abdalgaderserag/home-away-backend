<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'sender_id' => Auth::id(),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'project_id' => 'exists:projects,id|unique:chats,project_id',
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'required|exists:users,id|different:sender_id',
            'context' => 'required_without:attachments|nullable|string',
            'attachments' => 'required_without:context|array|size:3|exists:attachments,id'
        ];
    }

    public function messages()
    {
        return [
            'receiver_id.different' => 'Receiver must be different from sender',
            'context.required_without' => 'Message must have either text or attachment',
            'attachments.required_without' => 'Message must have either text or attachment',
        ];
    }
}
