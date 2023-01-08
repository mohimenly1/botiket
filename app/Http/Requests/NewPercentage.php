<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewPercentage extends FormRequest
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
            "brand_id" =>"required|exists:brands,id",
            "category_id" => "required|exists:categories,id",
            "sub_category_id" => "nullable|unique:percentages,sub_category_id|exists:sub_categories,id",
            "increase" => "required"
        ];
    }
}
