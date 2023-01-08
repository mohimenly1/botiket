<?php

namespace App\Http\Requests;

use App\Http\Requests\AbstractFormRequest;
use Auth;
use Illuminate\Validation\Rule;

class CollectionRequest extends AbstractFormRequest
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
            'description'=>'required|string',
            'products' => 'required|array',
            
        ];
      
        $put_rules =  [
            'description'=>'required|string',
            'products' => 'required|array',
        ];
        
        if(Auth::user()->role=='super-admin'){
            $post_rules['products.*'] = $put_rules['products.*']='required|exists:products,id';
        }else{
            $post_rules['products.*'] = $put_rules['products.*'] ='required|exists:products,id,store_id,'.Auth::user()->store()->first()->id;
        }
        if ($this->getMethod()== 'POST') {
            return $post_rules;
        } elseif ($this->getMethod()== 'PUT' || $this->getMethod()== 'PATCH') {
            return $put_rules;
        }
    }
}
