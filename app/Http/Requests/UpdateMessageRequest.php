<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMessageRequest extends FormRequest
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
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'required|exists:users,id|different:sender_id',
            'context' => 'required_without:attachment|nullable|string',
            'attachment' => 'required_without:context|nullable|json',
        ];
    }
    public function messages()
    {
        return [
            'receiver_id.different' => 'Receiver must be different from sender',
            'context.required_without' => 'Message must have either text or attachment',
            'attachment.required_without' => 'Message must have either text or attachment',
        ];
    }
}
