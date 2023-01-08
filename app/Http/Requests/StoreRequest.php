<?php

namespace App\Http\Requests;

use App\Http\Requests\AbstractFormRequest;
use Auth;
use Illuminate\Validation\Rule;

class StoreRequest extends AbstractFormRequest
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
        $post_rules =  [
            'name'=>'required|string',
            'logo'=>'required|nullable|mimes:jpeg,jpg,png,gif,webp|image',
            'class_a_access'=>'required|boolean',
            'city_id' => 'required|exists:cities,id',
            'phone'=>'numeric|min:10|unique:stores,phone,'.request('id'),
            'description'=>'required|string',
            'longitude'=>'required|string',
            'latitude'=>'required|string',
            'admins_ids' => 'required|array',
            'admins_ids.*' =>  'required|exists:users,id,role,store-admin|unique:store_user,user_id',

        ];
        $put_rules =  [
            'name'=>'string',
            'logo'=>'nullable|mimes:jpeg,jpg,png,gif,webp|image',
            'has_sales'=>'boolean',
            'is_store_of_the_week'=>'boolean',
            'is_featured'=>'boolean',
            'class_a_access'=>'boolean',
            'is_active'=>'boolean',
            'city_id' => 'exists:cities,id',
            'phone'=>'numeric|min:10|unique:stores,phone,'.request('id'),
            'description'=>'string',
            'longitude'=>'string',
            'latitude'=>'string',
            'admins_ids' => 'array',
            'admins_ids.*' =>  'exists:users,id,role,store-admin|'.
            Rule::unique('store_user', 'user_id')->ignore(request('store'),'store_id'),
            
        ];
        if ($this->getMethod()== 'POST') {
            return $post_rules;
        } elseif ($this->getMethod()== 'PUT' || $this->getMethod()== 'PATCH') {
            return $put_rules;
        }
    }
}
