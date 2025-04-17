<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUsersRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            '*.first_name' => 'required|string|max:255',
            '*.last_name' => 'required|string|max:255',
            '*.email' => 'required|email|unique:users,email',
            '*.mobile' => 'nullable|string|max:15|unique:users,mobile',
            '*.address' => 'nullable|string|max:255',
            '*.date_of_birth' => 'required',
            '*.password' => 'required|string|min:8|confirmed',
        ];
    }

    public function message(){
        return [
            '*.first_name.required' => 'First name is required.',
            '*.last_name.required' => 'Last name is required.',
            '*.email.required' => 'Email is required.',
            '*.mobile.required' => 'Mobile number is required.',
            '*.address.required' => 'Address is required.',
            '*.date_of_birth.required' => 'Date of birth is required.',
            '*.password.required' => 'Password is required.',
        ];
    }

    protected function failedValidation(Validator $validator){
        $message = $validator->errors()->getMessageBag();
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => $message
        ], 422));
    }
}
