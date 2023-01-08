<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseTraits;
use App\Services\BannerService;
class BannerController extends Controller
{    use ResponseTraits;
    protected $banner_service;
    public function __construct(BannerService $service)
    {
        $this->banner_service = $service;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $data = $this->banner_service->index($request);
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
            $data = $this->banner_service->store($request);
            return $data;
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
             $data = $this->banner_service->show($id);
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
             $data = $this->banner_service->update($request, $id);
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
            $data = $this->banner_service->destroy($id);
            return $data;
        } catch (\Exception $e) {
            return $data;
        }
    }

    
}
