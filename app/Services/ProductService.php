<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Auth;
use Illuminate\Http\Request;

class ProductService
{
    protected $product;

    public function __construct(Product $product)
    {
        $this->product = new ProductRepository($product);
    }
    /**
       * Display a listing of the resource.
       */
    public function index()
    {
        return $this->product->index();
    }
    /**
     * Display a listing of the filtered resource.
     *
     */
    public function filterProducts($request)
    {
        return $this->product->filterProducts($request);
    }

    /**
     * Display a listing of the resource.
     */
    public function skus($search)
    {
        return $this->product->skus($search);
    }
    /**
       * Display a listing of the resource.
       */
    public function indexDeleted()
    {
        return $this->product->indexDeleted();
    }
   
    /**
     * Store a newly created resource in storage.
     */
    public function store($request)
    {
        return $this->product->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return $this->product->showWithRelation($id, ['medias.color','quantities.color','brand.sellingCurrency','brand.originalCurrency','offer']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($request, $id)
    {
        return $this->product->update($request, $id);
    }

    /*
    Update Product Price
    */
    public function update_price($rate1, $rate2, $fee, $id)
    {
        return $this->product->update_price($rate1, $rate2, $fee, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return $this->product->destroy($id);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        return $this->product->restore($id);
    }
    /**
    * Display a listing of the products.
     */
    public function quantities($id)
    {
        return $this->product->quantities($id);
    }/** /**
    * Display a listing of the products.
     */
    public function data()
    {
        return $this->product->data();
    }/**
    * Display a listing of the products.
     */
    public function brands()
    {
        return $this->product->brands();
    }
    public function search($request)
    {
        return $this->product->search($request);
    }
    /**
    * Display a listing of the genders.
    */
    public function genders()
    {
        return $this->product->genders();
    }
    /**
    * Display a listing of the colors.
    */
    public function colors()
    {
        return $this->product->colors();
    }
    /**
       * Display a listing of the offers.
       */
    public function offers()
    {
        return $this->product->offers();
    }
    /**
      * Display a listing of the main Categories.
      */
    public function mainCategories($gender)
    {
        return $this->product->mainCategories($gender);
    }
    /**
     * Display a listing of the sub Categories.
     */
    public function subCategories($category)
    {
        return $this->product->subCategories($category);
    }
    /**
    * Display a listing of the resource with Filtering.
       */
    public function filter($request)
    {
        return $this->product->filter($request);
    }

    public function report($request)
    {
        return $this->product->report($request);

    }
    /************************** Class B **********************************/

    /**
     * Display a listing of the resource.
     */
    public function classBIndex($request)
    {
        return $this->product->classBIndex($request);
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function classBStore($request)
    {
        return $this->product->classBStore($request);
    }
  
    /**
     * Display the specified resource.
     */
    public function classBShow($id)
    {
        return $this->product->classBShow($id, ['medias','quantities']);
    }
  
    /**
     * Update the specified resource in storage.
     */
    public function classBUpdate($request, $id)
    {
        return $this->product->classBUpdate($request, $id);
    }
  
    /**
     * Remove the specified resource from storage.
     */
    public function classBDestroy($id)
    {
        return $this->product->classBDestroy($id);
    }
    /**
     * get product Details.
     */
    public function productDetails($id)
    {
        return $this->product->productDetails($id);
    }

    
    public function skuProductDetails($sku)
    {
        return $this->product->skuProductDetails($sku);

    }
}
