<?php

namespace App\Http\Requests;

use App\Http\Requests\AbstractFormRequest;
use Auth;
use Illuminate\Validation\Rule;


class ProductRequest extends AbstractFormRequest
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

            'store_id' => 'nullable|exists:stores,id',
            'sku'=>'required|string|min:3',
            'title'=>'required|string',
            'description'=>'nullable|string',
            'price'=>'required|numeric',
            'is_shipped'=>'required|boolean',
            'is_featured'=>'required|boolean',
            'brand_id' => 'exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id,category_id,'.request('category_id'),
            'offer_id' => 'nullable|exists:offers,id',
            'gender_id' => 'required|exists:genders,id',
            'medias' => 'required|array',
            'medias.*.file' => 'required|image|mimes:png,jpg,jpeg,svg,webp',
            'quantities' => 'required|array',
            'quantities.*.color' => 'required|exists:colors,color_value',
            'quantities.*.size'=>'required|string',
            'quantities.*.quantity'=>'required|numeric',

            
        ];
    }
}
