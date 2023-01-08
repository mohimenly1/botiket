<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseTraits;
use App\Services\DeliveryService;
class DeliveryController extends Controller
{    use ResponseTraits;
    protected $delivery_service;
    public function __construct(DeliveryService $service)
    {
        $this->delivery_service = $service;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = $this->delivery_service->index();
            return $this->prepare_response( null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response( [$e], __('auth.Something went wrong'), null, 400);
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
            $data = $this->delivery_service->indexDeleted();
            return $this->prepare_response( null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response( [$e], __('auth.Something went wrong'), null, 400);
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
        try {
            $data = $this->delivery_service->store($request);
            return $this->prepare_response( null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response( [$e], __('auth.Something went wrong'), null, 400);
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
             $data = $this->delivery_service->show($id);
            return $this->prepare_response( null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response( [$e], __('auth.Something went wrong'), null, 400);
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
             $data = $this->delivery_service->update($request, $id);
            return $this->prepare_response( null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response( [$e], __('auth.Something went wrong'), null, 400);
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
            $data = $this->delivery_service->destroy($id);
            return $this->prepare_response( null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response( [$e], __('auth.Something went wrong'), null, 400);
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
            $data = $this->delivery_service->restore($id);
            return $this->prepare_response( null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response( [$e], __('auth.Something went wrong'), null, 400);
        }
    }
   /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function report(Request $request)
    {
        try {
            $data = $this->delivery_service->report($request);
            return $this->prepare_response( null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response( [$e], __('auth.Something went wrong'), null, 400);
        }
    }
    
}
