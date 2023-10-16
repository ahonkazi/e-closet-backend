<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductStoreRequest extends FormRequest
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
            'title'=>'required|string|max:255',
            'discription'=>'required|string|min:100',
            'category_id'=>'required|numeric',
            'sub_category_id'=>'required|numeric',
            'sub_sub_category_id'=>'nullable|numeric',
            'product_image'=>'required|image|dimensions:width=300,height=439'
            
        ];
    }
            public function failedValidation(Validator $validator)
            {
                $data = ['message'=>'Validation Error',
        'errors'=>$validator->errors()];
                throw new HttpResponseException(response()->json($data,403));       
            }
}
