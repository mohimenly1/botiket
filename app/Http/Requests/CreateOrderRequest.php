<?php

namespace App\Http\Requests;

use App\Http\Requests\AbstractFormRequest;
use Auth;
use Illuminate\Validation\Rule;

class CreateOrderRequest extends AbstractFormRequest
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
            'payment_method_id' => 'required|exists:payment_methods,id',
            'coupons' => 'nullable|array',
            'coupons.*' => Rule::exists('coupons', 'code')->whereNot('usage_count', 0),

        ];




        return $post_rules;
    }
    public function messages()
    {
        return [
            'coupons.*.exists' => 'coupons.*',
            // 'body.required'  => 'A message is required',
        ];
    }

}
