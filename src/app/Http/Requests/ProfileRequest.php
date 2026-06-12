<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:20',
            'postcode' => 'required|string|size:8',
            'address' => 'required|string',
            'building' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png'
        ];
    }

    #[Override]
    public function messages()
    {
        return [
            'name.required' => 'お名前を入力してください',
            'name.max' => '20文字以内で入力してください',
            'postcode.required' => '郵便番号を入力してください',
            'postcode.size' => 'ハイフンありの8文字で入力してください',
            'address.required' => '住所を入力してください',
            'image.mimes' => '画像の形式は.jpegもしくは.pngのいずれかを選択してください',
        ];
    }
}
