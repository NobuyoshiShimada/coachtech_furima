<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class AddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return  true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'postcode' => 'required|string|size:8',
            'address' => 'required|string',
            'building' => 'nullable|string',
        ];
    }

    #[Override]
    public function messages()
    {
        return [
            'postcode.required' => '郵便番号を入力してください',
            'postcode.size' => 'ハイフンありの8文字で入力してください',
            'address.required' => '住所を入力してください',
        ];
    }
}
