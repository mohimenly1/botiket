<?php

namespace App\Http\Requests;

use App\Http\Requests\AbstractFormRequest;
use Auth;
use Illuminate\Validation\Rule;


class CouponRequest extends AbstractFormRequest
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

            'code'=>'nullable|unique:coupons,code|string|min:8|max:8',
            'is_percentage' => 'required|boolean',
            'value'=>'required|numeric',
            'usage_count'=>'string',
            'store_id' => 'nullable|exists:stores,id',
            'coupon_count'=>'numeric|required_without:code',
        ];
    }
}
