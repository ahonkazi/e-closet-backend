<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRegistrationRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'firstname'=>'required|string|max:255',
            'lastname'=>'required|string|max:255', 
            'email'=>'required|string|max:255|email|unique:users',
            'password'=>'required|string|min:8|confirmed',
            'profile_pic'=>'required|image',
            'country'=>'required|string|max:255',
            'city'=>'required|string|max:255',
            'profession'=>'required|string|max:255',
            'date_of_birth'=>'required|date',
            'gander'=>'required|string|max:20',
            'otp'=>'required|numeric',            
        ];
    }
    
        public function failedValidation(Validator $validator)
        {
            $data = ['message'=>'Validation Error',
                    'errors'=>$validator->errors()];
            throw new HttpResponseException(response()->json($data,401));       
        }

}
