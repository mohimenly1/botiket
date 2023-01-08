<?php

namespace App\Http\Requests;

use App\Http\Requests\AbstractFormRequest;
use Auth;
use Illuminate\Validation\Rule;
use App\Rules\OldPasswordRule;

class UpdateProfileRequest extends AbstractFormRequest
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
            'old_password' => 'required_with:password',new OldPasswordRule(Auth::id()),
            'name'=>'string|min:3',
            'phone'=>'numeric|min:10|unique:users,phone,'.Auth::id(),
            'password'=>'sometimes|nullable|min:6|string|confirmed',
            'image'=>'sometimes|nullable|mimes:jpeg,jpg,png,gif,webp|image'
            
        ];
    }

}
