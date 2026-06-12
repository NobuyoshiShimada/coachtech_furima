<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
class ExhibitionRequest extends FormRequest
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
            'image' => 'required|image|mimes:jpeg,png',
            'categories' => 'required|array|min:1',
            'condition_id' => 'required|exists:conditions,id',
            'name' => 'required|string',
            'description' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
        ];
    }

    public function messages()
    {
        return [
            'image.required' => '商品画像をアップロードしてください',
            'image.image' => '商品画像をアップロードしてください',
            'image.mimes' => '「.jpg」または「.png」形式でアップロードしてください',
            'categories.required' => 'カテゴリーを1つ以上選択してください',
            'categories.min' => 'カテゴリーを1つ以上選択してください',
            'condition_id.required' => '商品の状態を選択をしてください',
            'name.required' => '商品名を入力してください',
            'description.required' => '商品説明を入力してください',
            'description.max' => '255文字以内で入力してください',
            'price.required' => '販売価格を入力してください',
            'price.integer' => '販売価格は半角数字で入力してください',
            'price.min' => '販売価格は0円以上で入力してください',
        ];
    }
}
