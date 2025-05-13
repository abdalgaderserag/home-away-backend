<?php

namespace App\Http\Requests\Auth;

use App\Enum\User\UserType;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return !Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "name" => "required|string|min:3|max:25",
            'email' => [
                'required_without:phone',
                'email',
                Rule::unique('users')->whereNull('deleted_at'),
                function ($attribute, $value, $fail) {
                    if (User::where('email', $value)
                        ->whereNotNull('password')
                        ->exists()
                    ) {
                        $fail('This email is already registered with password-based login.');
                    }
                }
            ],
            'phone' => 'required_without:email|string|unique:users,phone',
            "password" => [
                "required",
                "string",
                "min:8",
            ],
            "type" => 'required|in:client,designer',
        ];
    }
}
