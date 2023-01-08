<?php

namespace App\Http\Middleware;

use App\Models\Currency;

use Closure;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseTraits;
use App\Repositories\CurrencyRepository;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class UpdateCurrenciesRate
{
    protected $currency_repo;

    use ResponseTraits;

    protected $currencies_service;

    public function __construct(Currency $currency)
    {
        $this->currency_repo = new CurrencyRepository($currency);
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
       try {

           
            //call the rate api to get the lattest
            $response = Http::get('https://freecurrencyapi.net/api/v2/latest?apikey=1ec65e50-83fe-11ec-bd43-25715ad4f7a2&base_currency=USD');
            
            $response->throw();

            
            DB::beginTransaction();
            
                //getting all currenices
                $CurrencyPairs = $this->currency_repo->get_all_usd();

                foreach ($CurrencyPairs as $CurrencyPair) {
                    
                    if( isset($response->json()['data'][$CurrencyPair->base]) ){
                        $CurrencyPair->rate = 1/($response->json()['data'][$CurrencyPair->base]);
                        $CurrencyPair->reverse_rate = $response->json()['data'][$CurrencyPair->base];
                        $CurrencyPair->save();
                    }/*else{
                        return response()->json($CurrencyPair->base);
                    }*/
                    
                    
                }
           DB::commit();

       } catch (\Exception $e) {

           return $this->prepare_response(__('auth.Something went wrong'), __('auth.Something went wrong'), $e, 500);
       }
         return $next($request);





        
    }
}
