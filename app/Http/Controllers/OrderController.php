<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Requests\PaymentMethodRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseTraits;
use App\Services\OrderService;

class OrderController extends Controller
{
    use ResponseTraits;
    protected $order_service;
    public function __construct(OrderService $service)
    {
        $this->order_service = $service;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = $this->order_service->index();
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
            $data = $this->order_service->indexDeleted();
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


    public function store(OrderRequest $request)
    {
        try {
            $data = $this->order_service->store($request);
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
       // try {
            $data = $this->order_service->show($id);
           return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
     //   } catch (\Exception $e) {
     //       return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
    //    }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(OrderRequest $request, $id)
    {
        // try {

            $data = $this->order_service->update($request, $id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        // } catch (\Exception $e) {
        //     return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        // }
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
            $data = $this->order_service->destroy($id);
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
            $data = $this->order_service->restore($id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * Return back the count for all the orders with each statue .
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function statistics()
    {
        try {
            $data = $this->order_service->statistics();
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }


    /************************** Class B **********************************/
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function classBIndex(Request $request)
    {
        try {
            $data = $this->order_service->classBIndex($request);
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
    public function classBStore(Request $request)
    {
        try {
            $data = $this->order_service->classBStore($request);
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
    public function classBUpdate(Request $request, $id)
    {
        try {
            $data = $this->order_service->classBUpdate($request, $id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**************************    Application APIS  **********************************/

    /**
     * get all orders.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userOrders()
    {
        try {
            $data = $this->order_service->userOrders();
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * get order details
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ordersDetails($id)
    {
        // try {
            $data = $this->order_service->ordersDetails($id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        // } catch (\Exception $e) {
        //     return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        // }
    }
    /**
     * cancels the order if its status is "تم التعليق" or "قيد المراجعة
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancelOrder($id)
    {
        try {
            $data = $this->order_service->cancelOrder($id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
     * confirms the order if its status is "تم التعليق"
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function confermOrder($id)
    {
        // try {
            $data = $this->order_service->confermOrder($id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        // } catch (\Exception $e) {
        //     return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        // }
    }
    /**
     * adds transaction to an order only if its status is "قيد التجهيز
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addTransaction(Request $request, $id)
    {
        try {
            $data = $this->order_service->addTransaction($request, $id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
     * changes the order payment method only if its status is "قيد المراجعة"
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function paymentMethod(PaymentMethodRequest $request, $id)
    {
        try {
            $data = $this->order_service->paymentMethod($request, $id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

}
