<?php

namespace App\Services;

use App\Models\CartItem;
use App\Repositories\CartRepository;
use Auth;
use Illuminate\Http\Request;

class CartService
{
    protected $cart;

    public function __construct(CartItem $cart)
    {
        $this->cart = new CartRepository($cart);
    }
    /**
       * Display a listing of the resource.
       */
    public function index()
    {
        return $this->cart->index();
    }
 
    /**
     * Store a newly created resource in storage.
     */
    public function store($request)
    {
        return $this->cart->store($request);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($request, $id)
    {
        return $this->cart->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return $this->cart->destroy($id);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        return $this->cart->restore($id);
    }
    /**
     * Get pre order data.
     */
    public function preOrder($request)
    {
        return $this->cart->preOrder($request);
    }
    /**
     * create order.
     */
    public function order(Request $request)
    {
        return $this->cart->order($request);

    }
}
