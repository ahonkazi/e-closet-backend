<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductStockStoreRequest extends FormRequest
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
            'primary_option_id'=>'required|numeric',
            'secondary_option_id'=>'nullable|numeric',
            'price'=>'required|numeric',
            'discount_in_percent'=>'required|numeric',
            'stock'=>'required|numeric',
            'image'=>'required|image|dimensions:width=300,height=439'
            
        ];
    }
            public function failedValidation(Validator $validator)
            {
                $data = ['message'=>'Validation Error',
        'errors'=>$validator->errors()];
                throw new HttpResponseException(response()->json($data,401));       
            }
}
