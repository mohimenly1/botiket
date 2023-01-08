<?php

namespace App\Services;

use App\Models\WishlistItem;
use App\Repositories\WishlistRepository;
use Auth;
use Illuminate\Http\Request;

class WishlistService
{
    protected $wishlist;

    public function __construct(WishlistItem $wishlist)
    {
        $this->wishlist = new WishlistRepository($wishlist);
    }
    /**
       * Display a listing of the resource.
       */
    public function index()
    {
        return $this->wishlist->index();
    }
    /**
       * Display a listing of the resource.
       */
    public function indexDeleted()
    {
        return $this->wishlist->indexDeleted();
    }
   
    /**
     * Store a newly created resource in storage.
     */
    public function store($id)
    {
        return $this->wishlist->store($id);
    }
    /**
     * add wishlist item to cart.
     */
    public function wishlistToCart($request, $id)
    {
        return $this->wishlist->wishlistToCart($request, $id);
    }

       

    
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return $this->wishlist->show($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($request, $id)
    {
        return $this->wishlist->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($request)
    {
        return $this->wishlist->destroy($request);
    }
    public function destroySinleItem($id)
    {
        return $this->wishlist->destroySinleItem($id);
    }
    

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        return $this->wishlist->restore($id);
    }  
    /**
    * Add discount To the specified resource from storage.
    */
   public function discount($request,$product)
   {
       return $this->wishlist->discount($request,$product);
   }
    /**
     * get wishlist items.
     */
    public function userItems()
    {
        return $this->wishlist->userItems();
    }
}
