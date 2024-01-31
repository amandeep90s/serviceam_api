<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProviderSignUpRequest extends FormRequest
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
            "social_unique_id" => [
                "required_if:login_by,GOOGLE,FACEBOOK",
                "unique:providers",
            ],
            "device_type" => "in:ANDROID,IOS",
            "first_name" => "required|max:255",
            "last_name" => "required|max:255",
            "country_code" => "required",
            "email" => "required|email|max:255",
            "password" => ["required_if:login_by,MANUAL", "min:6"],
            "salt_key" => "required",
        ];
    }
}
