<?php

namespace App\Http\Requests;

use App\Http\Requests\AbstractFormRequest;
use Auth;
use Illuminate\Validation\Rule;

class OfferRequest extends AbstractFormRequest
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
            'expire_date'=>'required|date_format:Y-m-d|after:today',
            'is_percentage'=>'required|boolean',
            'value'=>'required|numeric',
            'products' => 'required|array',
            
        ];
      
        $put_rules =  [
            'name'=>'string',
            'expire_date'=>'date_format:Y-m-d|after:today',
            'is_percentage'=>'boolean',
            'value'=>'numeric',
            'products' => 'required|array',
        ];
        
        if(Auth::user()->role=='super-admin'){
            $post_rules['products.*'] ='required|exists:products,id';
            $put_rules['products.*'] ='nullable|exists:products,id';

        }else{
            // $post_rules['products.*'] ='required|exists:products,id,store_id,'.Auth::user()->store()->first()->id;
            
            // $put_rules['products.*'] ='nullable|exists:products,id,store_id,'.Auth::user()->store()->first()->id;
        }
        if ($this->getMethod()== 'POST') {
            return $post_rules;
        } elseif ($this->getMethod()== 'PUT' || $this->getMethod()== 'PATCH') {
            return $put_rules;
        }
    }
}
