<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Currency;
use App\Repositories\CurrencyRepository;
use App\Repositories\BrandRepository;
use App\Repositories\ProductRepository;
use App\Services\ProductService;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;


class BrandService
{
    protected $brand;
    protected $product_repo;
    protected $currency_repo;


    public function __construct(Brand $brand, Product $product, Currency $currency )
    {
        $this->currency_repo = new CurrencyRepository($currency);
        $this->brand = new BrandRepository($brand);
        $this->product_repo = new ProductRepository($product);

    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->brand->index();
    }
    public function currencies()
    {
        return $this->brand->currencies();
    }
    /* Display a listing of the resource if brand have products.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexHasProducts()
    {
        return $this->brand->indexHasProducts();

    }
    /**
     * Display a listing of the resource.
     */
    public function indexDeleted()
    {
        return $this->brand->indexDeleted();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($request)
    {
        return $this->brand->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return $this->brand->showUser($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($request, $id)
    {
        return $this->brand->update($request, $id);
    }

    public function update_brands($request, $brands )
    {
        
        foreach ($brands as $brand) {

          //  $brand_products = $this->brand->products($brand->id);
            
            $brand->products()->chunk(50, function($brand_products) use($brand) {
                
                 foreach ($brand_products as $product ) {

               $rates = $this->get_brand_rate($brand);
               $percentage = $product->subCategory->percentage ? $product->subCategory->percentage->increase : $brand->increase_percentage;
               $updated_product = $this->product_repo->update_price( $rates->rate1,$rates->rate2, $percentage, $product );
               $updated_product->save();

            }
            });


        }
    }

  
    //check if user inputed price matches
    public function check_product_price($request, $brand )
    {
        $rates = $this->get_brand_rate($brand);
        $brand_product = Product::find($request->id);
        if( $brand_product->price < $brand_product->original_price * $rates->rate1 * $rates->rate2 ){
            throw new \ErrorException('Error found');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return $this->brand->destroy($id);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        return $this->brand->restore($id);
    }
    /**
     * get products by brand id
     */
    public function products($id)
    {
        return $this->brand->products($id);
    }


    public function product_is_shipped(){

        return $this->brand->product_is_shipped();
    }

    //getting the brand first & second rate
    public function get_brand_rate($brand){

        if( !($brand  instanceof Brand) ){
            if( !is_numeric($brand) ){

                throw new \ErrorException('Unvalid Value');
            }
        $brand = $this->brand->showUser($brand);
        }

        $rate1 = 1;
        $rate2 = 1;

        //return dd($this->currency_repo->get($brand->original_currency_id)->rate);

        if( $brand->original_currency_id !== $brand->selling_currency_id ){

            $rate1 = $this->currency_repo->get($brand->original_currency_id)->rate;
            $rate2 = $this->currency_repo->get($brand->selling_currency_id)->reverse_rate;

        }

        $rate_obj = new \stdClass();

        $rate_obj->rate1 = $rate1;
        $rate_obj->rate2 = $rate2;

        return $rate_obj;

    }

}
