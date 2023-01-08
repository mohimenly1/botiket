<?php

namespace App\Http\Requests;

use App\Rules\OldPasswordRule;
use App\Http\Requests\AbstractFormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
class RegisterRequest extends AbstractFormRequest
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
            'title'=>'required|string',
            'city_id' => 'required|exists:cities,id',
            'latitude'=>'string',
            'longitude'=>'string',
            'fcm_token' =>'nullable|string',
        ];
        $put_rules =   [
           
        ];
        if ($this->getMethod()== 'POST') {
            return $post_rules;
        } elseif ($this->getMethod()== 'PUT' || $this->getMethod()== 'PATCH') {
           
            return $put_rules;
        }
    }

}