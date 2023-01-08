<?php

namespace App\Http\Requests;
use App\Http\Requests\AbstractFormRequest;
class UserRequest extends AbstractFormRequest
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
            'name'=>'required|string|min:3',
            'phone'=>'required|numeric|min:10|unique:users',
            'password'=>'required|min:6|string|confirmed',
            'image'=>'mimes:jpeg,jpg,png,gif,webp|image',
            'store_id' => 'nullable|exists:stores,id',
           
            
        ];
        $put_rules =   [
            'name'=>'string|min:3',
            'phone'=>'numeric|min:10|unique:users,phone,'.request('id'),
            'password'=>'sometimes|nullable|min:6|string|confirmed',
            'image'=>'sometimes|nullable|mimes:jpeg,jpg,png,gif,webp|image',
            'store_id' => 'nullable|exists:stores,id',

        ];
        if ($this->getMethod()== 'POST') {
            return $post_rules;
        } elseif ($this->getMethod()== 'PUT' || $this->getMethod()== 'PATCH') {
           
            return $put_rules;
        }
    }
}
