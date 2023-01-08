<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseTraits;
use App\Services\BrandService;
use Illuminate\Support\Facades\DB;
use App\Repositories\CurrencyRepository;
use App\Models\Currency;



class BrandController extends Controller
{
    use ResponseTraits;

    protected $brand_service;
    protected $currency_repo;

    public function __construct(BrandService $service, Currency $currency)
    {
        $this->brand_service = $service;
        $this->currency_repo = new CurrencyRepository( $currency );
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = $this->brand_service->index();
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
     * Display a listing of the resource if brand have products.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexHasProducts()
    {
        try {
            $data = $this->brand_service->indexHasProducts();
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }


    public function currencies()
    {
        try {
            $data = $this->brand_service->currencies();
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
        * Display a listing of the resource.
        *
        * @return \Illuminate\Http\Response
        */
    public function indexDeleted()
    {
        try {
            $data = $this->brand_service->indexDeleted();
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    public function store(Request $request)
    {

        $request->validate([

            'original_currency_id' => 'required|integer|exists:currencies,id',
            'selling_currency_id' => "required|integer|exists:currencies,id",
            'increase_percentage' => "required|min:1|numeric"
        ]);

        // selling_currency_id must be the same as original_currency_id or LYDUSD which is 69
        if($request->original_currency_id != $request->selling_currency_id && $request->selling_currency_id != 69){
            return $this->prepare_response([], __('auth.Something went wrong'), null, 400);
        }

        try {
           // return $request;
            $data = $this->brand_service->store($request);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            dd($e);
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $data = $this->brand_service->show($id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            // dd($request->all());
            $data = $this->brand_service->update($request, $id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $data = $this->brand_service->destroy($id);
            return $data;
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        try {
            $data = $this->brand_service->restore($id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * get products by brand id
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function products($id)
    {
        try {
            $data = $this->brand_service->products($id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    public function product_is_shipped(){
        try {
            $data = $this->brand_service->product_is_shipped();
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }




    public function update_percentage( Request $request){
        
        try {
    
            $request->validate([
                'increase_percentage' => "required|min:1|numeric",
                'id' => 'required|numeric'
            ]);

            $id = $request->id;
            DB::transaction(function () use ($request, $id) {

               // update update_percentage in the table
              $this->brand_service->update($request, $id);
               //update the product
               $brands_to_update = Brand::where('id', $id)->get();

               $this->brand_service->update_brands($request, $brands_to_update);

            });

            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), null, 200);

        } catch (\Exception $e) {
            //return $e;
            return $this->prepare_response($e->getMessage(), __('auth.Something went wrong'), null, 400);

        }
    }

    public function update_local_rate( Request $request){

        try {

            $request->validate([
                'rate'=>'required|numeric|min:0.01',
            ]);

            $id = $request->id;
            DB::transaction(function () use ($request, $id) {

               // update local rate
               $this->currency_repo->update_local_rate($request->rate);
               //update the product
               $brands_to_update = Brand::all();

               return $this->brand_service->update_brands($request, $brands_to_update);

            });

            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), null, 200);

        } catch (\Exception $e) {

            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);

        }
    }


}
