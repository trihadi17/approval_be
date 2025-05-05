<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'id_card' => 'required|string|min:5|max:16|unique:users,id_card',
            'name' => 'required|string|min:2|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'address' => 'required|string|max:100',
            'gender' => 'required|in:Male,Female',
            'phone_number' => 'required|string|max:15',
            // 'profile_picture' => 'nullable|file|mimes:png,jpg',
            'role' => 'in:Admin,Verifikator,Ordinary',
            'is_verified' => 'boolean',
        ];
    }
}
