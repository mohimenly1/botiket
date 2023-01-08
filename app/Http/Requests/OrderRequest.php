<?php

namespace App\Http\Requests;

use App\Http\Requests\AbstractFormRequest;
use Auth;
use Illuminate\Validation\Rule;

class OrderRequest extends AbstractFormRequest
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
            'user_id'=>'exists:users,id,role,end-user',
            
            'user.*' =>'required_if:user_id,null',

            'user.name'=>'string|min:3',
            'user.phone'=>'numeric|min:10|unique:users',

            'order_status_id'=>'nullable|exists:order_status,id',
            'delivery_price'=>'nullable|numeric',
            'delivery_date'=>'required|date_format:Y-m-d',
            'delivery_id'=>'nullable|exists:deliveries,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'coupon_code'=> 'nullable|'.Rule::exists('coupons', 'code')->whereNot('usage_count', 0),
            'discount'=>'nullable|numeric',
            'products' => 'required|array',
            'products.*.quantity_id' =>'required|exists:quantities,id',
            
        ]; 
      
        $put_rules =  [
            'user_id'=>'nullable|exists:users,id,role,end-user',
            'order_status_id'=>'nullable|exists:order_status,id',
            'delivery_price'=>'nullable|numeric',
            'delivery_date'=>'nullable|date_format:Y-m-d',
            'delivery_id'=>'nullable|exists:deliveries,id',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'coupon_code'=>'nullable|'.Rule::exists('coupons', 'code')
            /*->whereNot('usage_count', 0)*/,
            'discount'=>'nullable|numeric',
            'products' => 'nullable|array',
            'products.*.quantity_id' =>'nullable|exists:quantities,id',
        ];
        
        if ($this->getMethod()== 'POST') {
            return $post_rules;
        } elseif ($this->getMethod()== 'PUT' || $this->getMethod()== 'PATCH') {

            return $put_rules;
        }
    }
}
