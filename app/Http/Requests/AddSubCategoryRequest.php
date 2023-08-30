<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddSubCategoryRequest extends FormRequest
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
            //
            'name'=>'required|string|max:50|min:3',
            'slug'=>'required|string|max:100|min:3',
            'category_id'=>'required|numeric'
        ];
    }
        public function failedValidation(Validator $validator)
        {
            $data = ['message'=>'Validation Error',
        'errors'=>$validator->errors()];
            throw new HttpResponseException(response()->json($data,401));       
        }
}
