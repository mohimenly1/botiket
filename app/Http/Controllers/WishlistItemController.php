<?php

namespace App\Http\Controllers;

use App\Models\WishlistItem;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseTraits;
use App\Services\WishlistService;
use App\Http\Requests\ProductRequest;

class WishlistItemController extends Controller
{
    use ResponseTraits;
    protected $wishlist_service;
    public function __construct(WishlistService $service)
    {
        $this->wishlist_service = $service;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // try {
            $data = $this->wishlist_service->index();
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        // } catch (\Exception $e) {
        //     return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        // }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexDeleted()
    {
        try {
            $data = $this->wishlist_service->indexDeleted();
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
    public function store($id)
    {
        try {
            $data = $this->wishlist_service->store($id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
     * add wishlist item to cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function wishlistToCart(Request $request, $id)
    {
        // try {
        $data = $this->wishlist_service->wishlistToCart($request, $id);
        return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        // } catch (\Exception $e) {
        //     return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        // }
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
            $data = $this->wishlist_service->show($id);
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
            $data = $this->wishlist_service->update($request, $id);
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
    public function destroy(Request $request)
    {
        try {
            $data = $this->wishlist_service->destroy($request);
            return $data;
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    public function destroySinleItem($id)
    {
        try {
            $data = $this->wishlist_service->destroySinleItem($id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
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
            $data = $this->wishlist_service->restore($id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
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
    public function discount(Request $request, $product)
    {
        try {
            $data = $this->wishlist_service->discount($request, $product);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {

            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * get wishlist items.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userItems()
    {
        try {
            $data = $this->wishlist_service->userItems();
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
}
