<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Order;
use App\Models\User;
use App\Repositories\OrderRepository;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OrderService
{
    protected $order;

    public function __construct(Order $order)
    {
        $this->order = new OrderRepository($order);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->order->index();
    }
    /**
     * Display a listing of the resource.
     */
    public function indexDeleted()
    {
        return $this->order->indexDeleted();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //check if we need to create a new user for the order
        if ( $request->has("user") && (!$request->has("user_id")) ) {
            
            //Create the New User
            $user = new User($request->user);
            $user->password = Hash::make($request->password);
            $user->save();
            
            $input= $request->all();
            $data = $input['user']['adress'];
            


            $adress = new Address( array_merge($data,["user_id" => $user->id]) );
            $adress->save();

            $request->merge([
                'user_id' => $user->id,
                'address_id' => $adress->id
            ]);
        }
        return $this->order->store($request);
    }



    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return $this->order->show($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($request, $id)
    {
        return $this->order->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return $this->order->destroy($id);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        return $this->order->restore($id);
    }
    /**
     * Return back the count for all the orders with each statue.
     */
    public function statistics()
    {
        return $this->order->statistics();
    }


    /************************** Class B **********************************/

    /**
     * Display a listing of the resource.
     */
    public function classBIndex($request)
    {
        return $this->order->classBIndex($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function classBStore($request)
    {
        return $this->order->classBStore($request);
    }


    /**
     * Update the specified resource in storage.
     */
    public function classBUpdate($request, $id)
    {
        return $this->order->classBUpdate($request, $id);
    }

    /**************************    Application APIS  **********************************/

    /**
     * get all orders.
     */
    public function userOrders()
    {
        return $this->order->userOrders();
    }

    /**
     * get order details
     */
    public function ordersDetails($id)
    {
        return $this->order->ordersDetails($id);
    }
    /**
     * cancels the order if its status is "تم التعليق" or "قيد المراجعة
     */
    public function cancelOrder($id)
    {
        return $this->order->cancelOrder($id);
    }
    /**
     * confirms the order if its status is "تم التعليق"
     */
    public function confermOrder($id)
    {
        return $this->order->confermOrder($id);
    }
    /**
     * adds transaction to an order only if its status is "قيد التجهيز
     *
     */
    public function addTransaction($request, $id)
    {
        return $this->order->addTransaction($request, $id);
    }

    /**
     * changes the order payment method only if its status is "قيد المراجعة"
     */
    public function paymentMethod($request, $id)
    {
        return $this->order->paymentMethod($request, $id);
    }
}
