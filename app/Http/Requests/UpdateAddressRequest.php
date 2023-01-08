<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\AbstractFormRequest;
use Auth;

class UpdateAddressRequest extends AbstractFormRequest
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
            'address_id'=>'required|exists:addresses,id,user_id,'. Auth::id(),
            'title'=>'required|string',
            'city_id' => 'required|exists:cities,id',
            'description'=>'string',
            'latitude'=>'string',
            'longitude'=>'string'
        ];
    }
}
