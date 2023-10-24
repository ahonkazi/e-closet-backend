<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CustomerAddressCreateRequest extends FormRequest
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
            'country' => 'required|string',
            'district' => 'required|string',
            'sub_district' => 'required|string',
            'street_address' => 'required|string',
            'appartment_number' => 'required|string',
            'postal_code' => 'required|string',
            'phone' => ['required', 'regex:/^(?:\+?88|0088)?01[3-9]\d{8}$/'],
            'address_type_no' => 'required|numeric',
        ];
    }
    public function failedValidation(Validator $validator)
    {
        $data = ['message' => 'Validation Error',
            'errors' => $validator->errors()];
        throw new HttpResponseException(response()->json($data, 400));
    }
}
