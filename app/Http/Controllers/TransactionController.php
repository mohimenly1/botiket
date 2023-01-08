<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseTraits;
use App\Services\TransactionService;

class TransactionController extends Controller
{
    use ResponseTraits;
    protected $transaction_service;
    public function __construct(TransactionService $service)
    {
        $this->transaction_service = $service;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = $this->transaction_service->index();
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
        try {
        $data = $this->transaction_service->store($request);
        return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
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
            $data = $this->transaction_service->show($id);
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
            $data = $this->transaction_service->update($request, $id);
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
    public function invoices()
    {
        try {
            $data = $this->transaction_service->invoices();
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
     * Return back the count for all the transaction with each statue .
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function statistics()
    {
        try {
            $data = $this->transaction_service->statistics();
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    public function report(Request $request)
    {
        // try {
            $data = $this->transaction_service->report($request);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        // } catch (\Exception $e) {
        //     return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        // }
    }
}
