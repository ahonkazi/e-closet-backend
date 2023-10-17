<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use LVR\Colour\Hex;

class ProductVariationOptionStoreRequest extends FormRequest
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
            'product_variation_id'=>'required|numeric',
            'value'=>'string|required',
            'color_code'=>['nullable', new Hex()],
            'product_image'=>'nullable|image|dimensions:width=542,height=542'
        ];
    }
       public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
       {
           $data = ['message'=>'Validation Error',
        'errors'=>$validator->errors()];
           throw new HttpResponseException(response()->json($data,400));
       }
}
