<?php

namespace App\Repositories;

use App\Helpers\FileHelper;
use App\Http\Traits\CrudTrait;
use App\Http\Traits\MainTrait;
use App\Http\Traits\ResponseTraits;
use App\Models\Currency;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

use Spatie\QueryBuilder\QueryBuilder;
use Tymon\JWTAuth\Facades\JWTAuth;

class CurrencyRepository
{
    use CrudTrait, ResponseTraits, MainTrait;
    /**
     * @var Delivery
     */
    protected $currency;

    /**
     * UserRepository constructor.
     *
     * @param Delivery $currency
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function get($id)
    {
        return Currency::findOrFail($id);
    }

    public function get_all_usd()
    {
        return Currency::where('quote','USD')
        ->where('base', '!=', 'USD')->where('base', '!=', 'LYD')
        ->get();
    }

    public function update($request, $id){
        
        $currency = Currency::findOrFail($id);
        $currency->fill($request->all());
        $currency->save();
    }


    public function update_local_rate($rate)
    {
        $currency = Currency::find(69);
        
        $currency->reverse_rate = $rate;
        $currency->rate = 1/$rate;

        $currency->save();
    }    



}
