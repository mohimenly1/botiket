<?php

namespace App\Repositories;

use App\Helpers\FileHelper;
use App\Http\Traits\CrudTrait;
use App\Http\Traits\MainTrait;
use App\Http\Traits\ResponseTraits;
use App\Models\Address;
use App\Models\BeamsNotification;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Currency;

use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Quantity;
use App\Models\Store;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Traits\NotificationTrait;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class CartRepository
{
    use CrudTrait, ResponseTraits, MainTrait, NotificationTrait;
    /**
     * @var Delivery
     */
    protected $brand;

    /**
     * UserRepository constructor.
     *
     * @param Delivery $brand
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
    /**
     * Get all brands with Role.
     *
     * @return Delivery $brand
     */
    public function index()
    {
        return $this->indexWhithCondetionAndRelationTrait(
            $this->model,
            ['user_id' => Auth::id()],
            ['relatedQuantity.color', 'products', 'products.store:id,name', 'products.firstMedia']
        );
    }


    /**
     * Get brand by id
     *
     * @param $id
     * @return mixed
     */
    public function showUserWithRelation($id, $relation)
    {
        return $this->showWithRelationTrait($this->model, $id, $relation);
    }
    /**
     *  Validate User And Provider data.
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */

    public function store($request)
    {
        try {
            DB::beginTransaction();
            foreach($request->products as $product){
                $quantity = Quantity::where(  [
                    'product_id' =>  (int)$product['product_id'],
                    'size' =>        $product['size'],
                    'color_id' =>    (int)$product['color_id']
                ])->first();
                if (!$quantity) {
                    return 'هذا الكمية غير متوفره حاليا';
                }
                $product['user_id'] = Auth::id();
                $product['quantity_id'] = $quantity->id;
                $card = $this->model->where('product_id',$product['product_id'])->where('user_id',$product['user_id'])->where('quantity_id',$product['quantity_id'])->first();
                if ($card) { 
                    $card->update([$card->quantity++]);    
                    // $card->quantity++;         
                    // $card->save();
                } else {
                    $card = $this->model->create(['product_id' =>$product['product_id'],'user_id' => $product['user_id'],'quantity_id' => $product['quantity_id'], 'color_id' => $product['color_id']]);
                    $card->save();
                }
             }
            DB::commit();
        }
        catch (Exception $e) {
            DB::rollBack();
            dd($e);
        }
      
        // // dd($request->all());
        // $quantity = Quantity::where(  [
        //     'product_id' =>  (int)$request->product_id,
        //     'size' =>        $request->size,
        //     'color_id' =>    (int)$request->color_id
        // ])->first();
        // if (!$quantity) {
        //     return 'هذا الكمية غير متوفره حاليا';
        // }
        // $request['user_id'] = Auth::id();
        // $request['quantity_id'] = $quantity->id;
        // $card = $this->model->where($request->except('size', ))->first();
        // if ($card) {
        //     $card->quantity++;
        //     dd('quantity++',$card);
        //     $card->save();
        // } else {
        //     $card = $this->model->fill($request->except('size'));

        //     $card->save();
        // }
      
    }

    /**
     * Update Profile
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */

    public function update($request, $cart)
    {
        $cartItem = CartItem::findOrFail($cart);
        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return $cartItem;
    }
    /**
     * destroy User
     *
     * @param $data
     * @return User
     */
    public function destroy($id)
    {
        return $this->destroyTrait($this->model, $id);
    }

    /**
     * restore User
     *
     * @param $data
     * @return User
     */
    public function restore($id)
    {
        return $this->restoreTrait($this->model, $id);
    }

    public function distanceCalculation($point1_lat, $point1_long, $point2_lat, $point2_long, $decimals = 2)
    {

        // Calculate the distance in degrees
        $degrees = rad2deg(
            acos(
                (sin(deg2rad((float) $point1_lat))
                    *
                    sin(deg2rad((float) $point2_lat)))
                    +
                    (cos(deg2rad((float) $point1_lat))
                        *
                        cos(deg2rad((float) $point2_lat))
                        *
                        cos(deg2rad((float) $point1_long - (float) $point2_long)))
            )
        );

        // Convert the distance in degrees to the chosen unit kilometres)

        $distance = $degrees * 111.13384; // 1 degree = 111.13384 km, based on the average diameter of the Earth (12,735 km)

        return round($distance, $decimals);
    }
    /**
     * Get pre order data.
     */
    public function preOrder($request)
    {

        //validate coupons
        $invalid_coupons = $invalid_products = $pre_order = $receivers = [];
        if ($request->has('coupons')) {
            $coupons = Coupon::whereIn('code', $request->coupons)->get();
                // return $coupons;
                foreach ($request->coupons as $coupon) {
                    $getCoupon=$coupons->where('code', $coupon)->first();
                    if (!$getCoupon || $getCoupon->usage_count <= 0) {
                        // dd($coupon);
                        $invalid_coupons[] = $coupon;
                    }
                }

        }

        //select carts groupBy groupBy
        $carts = $this->model
            ->with(['relatedQuantity', 'products.store', 'products.quantities'])
            ->where(['user_id' => Auth::id()])
            ->get()
            ->groupBy('products.store_id');



            //validate products quantity
        foreach ($carts as $cart) {
            foreach ($cart->pluck('products')->pluck('quantities') as $quantity) {
                if ($quantity->first()->quantity <= 0) {
                    $invalid_products[] = $quantity->first()->product()->pluck('title')->first();
                }
            }
        }

        if (count($invalid_coupons) > 0) {
            // dd($invalid_coupons);
            return $this->prepare_response(__('auth.Something went wrong'), ' الكوبون ' . $invalid_coupons[0] . ' غير صالح للاستخدام ', null, 422);
        } elseif ($invalid_products) {
            return $this->prepare_response(__('auth.Something went wrong'), 'برجاء المحاولة لاحقا ' . $invalid_products[0] . ' نفذت كمية المنتج', null, 422);
        }

        $i = $buy_value = $discount_value = $pre_order['total'] = 0;
        $pre_order['address'] = Auth::user()->address->load('city')->where('id', $request->address_id)->first();
        $pre_order['paymen_method'] = PaymentMethod::findOrFail($request->payment_method_id);
        $request['user_id'] = Auth::id();

        //local currency
        $local_currency = Currency::find(69);

        foreach ($carts as $cart) {
            $request['store_id'] = $cart->first()->products->store->id ?? null;
            $coupon_discount = $store_total_price = $coupon_discount = 0;

            $pre_order['stores'][$i]['id'] = $cart->first()->products->store->id ?? 0;
            $pre_order['stores'][$i]['name'] = $cart->first()->products->store->name ?? 'البويتك';

            //if its product of a brand
            if($pre_order['stores'][$i]['name'] == 'البويتك'){
                $pre_order['stores'][$i]['currency'] = $cart->first()->products->currency;
            }


            //in usd
            $buy_value = (array_sum($cart->pluck('old_price_usd')->all()));


            //in usd
            $price_after_discount = (array_sum($cart->pluck('price_usd')->all()));

            //in usd
            $discount_value = $buy_value - $price_after_discount;

            $store_total_price = $price_after_discount;

            if ($request->has('coupons')) {
                $store_coupon = $coupons
                    ->where('store_id', $cart->first()->products->store->id ?? null)
                    ->first();

                if ($store_coupon && $store_coupon->usage_count > 0) {
                    if ($store_coupon->is_percentage == 1) {
                        $coupon_discount = ($store_total_price * $store_coupon->value / 100);
                    } else {
                        $coupon_discount = $store_coupon->value * $local_currency->rate;
                    }
                    //in usd
                    $store_total_price = ($store_total_price - ($coupon_discount) );
                }
            }



            $pre_order['stores'][$i]['order'] = [

                //in lyd  (($buy_value in usd))
                'buy_value' => ceil($buy_value * $local_currency->reverse_rate),

                //in lyd  (($discount_value in usd))
                'discount_value' => intval($discount_value * $local_currency->reverse_rate),

                //in lyd  (($coupon_discount in lyd))
                'coupon_value' => ceil($coupon_discount * $local_currency->rate),


                'delivery_price' => Auth::user()->address->load('city')->where('id', $request->address_id)->first()->city->price

            ];

            $i++;

            $pre_order['total'] += ($store_total_price);
        }




        // adding local currency details
        $pre_order['local_currency'] = $local_currency;

        $pre_order['total'] = ceil($pre_order['total'] * $local_currency->reverse_rate) + (Auth::user()->address->load('city')->where('id', $request->address_id)->first()->city->price);
        return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $pre_order, 200);

    }


    /**
     * create order.
     */
    public function order($request)
    {
        DB::beginTransaction();
        $invalid_coupons = $invalid_products = [];
        $order = false;
        $user = Auth::user();

        if ($request->has('coupons')) {
            $coupons = Coupon::whereIn('code', $request->coupons)->get();
            foreach ($coupons as $coupon) {
                if ($coupon->usage_count <= 0) {
                    $invalid_coupons[] = $coupon->code;
                }
            }
        }

        $carts = $this->model
            ->with(['relatedQuantity', 'products.store', 'products.quantities'])
            ->where(['user_id' => $user->id])
            ->get()
            ->groupBy('products.store_id');

        // looping over each item in the cart to check if it exsit
        foreach ($carts as $cart) {
            foreach ($cart->pluck('products')->pluck('quantities') as $quantity) {
                if (Quantity::find($cart[0]->quantity_id)->quantity <= 0) {
                    $invalid_products[] = $quantity->first()->product()->pluck('title')->first();
                } else {
                    Quantity::where('id', $cart[0]->quantity_id)->decrement('quantity', 1); // quantity - 1
                }
            }
        }

        // $invalid_coupons will have coupon code or noting
        if ($invalid_coupons) {
            return $this->prepare_response(__('auth.Something went wrong'), ' ❌ الكوبون ' . $invalid_coupons[0] . ' غير صالح للاستخدام ', null, 422);
        } elseif ($invalid_products) {
            return $this->prepare_response(__('auth.Something went wrong'), ' ❌  برجاء المحاولة لاحقا ' . $invalid_products[0] . ' نفذت كمية المنتج', null, 422);
        }


        $pre_order = $receivers = [];
        $i = $buy_value = $discount_value = $pre_order['total'] = 0;
        $pre_order['address'] = Auth::user()->address->load('city')->where('id', $request->address_id)->first();
        $pre_order['paymen_method'] = PaymentMethod::findOrFail($request->payment_method_id);

        $request['user_id'] = $user->id;

        // getting the user authenticated user's delivery price based on the selected city address
        $request['delivery_price'] = Auth::user()->address->load('city')->where('id', $request->address_id)->first()->city->price;
        //getting the local currency Rate
        $local_currency = Currency::find(69);

        //looping over each store cart
        foreach ($carts as $cart) {

            //$cart is a list of current store(in the loop) cart items

            $invoice = [];
            $request['store_id'] = $cart->first()->products->store->id ?? null;
            $order = Order::create($request->all());
            $order->save();

            //looping over each cart item in the current store
            foreach ($cart as $item) {

                $order->items()->create([
                    'product_id' => $item->products->id,
                    'quantity' => $item->quantity,
                    'quantity_id' => $item->quantity_id,

                    //so the total amount is the new price ( which is the price after offer discount ) if there is no whishlist discount
                    //or it will be the cart item discount which is the new price plus the wishlist discount applied
                    'total_amount' => $item->price,
                ]);
            }


            $coupon_discount = $store_total_price = $coupon_discount = 0;

            $pre_order['stores'][$i]['id'] = $cart->first()->products->store->id ?? '0';
            $pre_order['stores'][$i]['name'] = $cart->first()->products->store->name ?? 'البويتك';




            // product pure price * quantity for each cart item to USD
            $buy_value = array_sum( $cart->pluck('old_price_usd')->all() ); // Converting to LYD



            // product new_price(or discount ( which is new_price plus wishlist discount ) ) * quantity for each cart item to LYD
            $price_after_discount = array_sum($cart->pluck('price_usd')->all());

            //every price refrence after this point is in LYD


            //total discounts ( wish list + each product own discount)
            $discount_value = $buy_value - $price_after_discount;

            // this store total paid
            $store_total_price = $price_after_discount;

            if ($request->has('coupons')) {

                $store_coupon = $coupons
                    ->where('store_id', $cart->first()->products->store->id ?? null )
                    ->first();

                if ($store_coupon && $store_coupon->usage_count > 0) {

                    if ($store_coupon->is_percentage == 1) {

                        $coupon_discount = ($store_total_price * $store_coupon->value / 100);

                    } else {

                        $coupon_discount = $store_coupon->value * $local_currency->rate;

                    }

                    $store_total_price = ($store_total_price - ($coupon_discount) );
                    $store_coupon->usage_count--;
                    $store_coupon->save();
                    $invoice['coupon_id'] = $store_coupon->id;
                }
                // var_dump($invoice);
                // printf('-');
            }


            $pre_order['stores'][$i]['order'] = [
                               //in lyd  (($buy_value in usd))
                               'buy_value' => ceil($buy_value * $local_currency->reverse_rate),

                               //in lyd  (($discount_value in usd))
                               'discount_value' => intval($discount_value * $local_currency->reverse_rate),

                               //in lyd  (($coupon_discount in lyd))
                               'coupon_value' => ceil($coupon_discount * $local_currency->rate),

                                "delivery_price" =>  Auth::user()->address->load('city')->where('id', $request->address_id)->first()->city->price
            ];


            $i++;
            $pre_order['total'] += $store_total_price;

            $order->total_amount = ceil($store_total_price * $local_currency->reverse_rate);
            $order->old_amount = ceil($buy_value * $local_currency->reverse_rate);

            $order->save();

            $invoice['rest_amount'] = ($order->total_amount);
            $invoice['discount'] = intval($discount_value * $local_currency->reverse_rate);

            $invoice['payment_method_id'] = $request->payment_method_id;

            $order->invoice()->create($invoice);
            if ($order->store_id) {
                $receivers[] = $order->store->users()->get()->first()->id;
            } else {
                $receivers = User::where('role', 'super-admin')->pluck('id')->toArray();
            }

           /* $this->send(
                $order->user_id,
                $receivers,
                "طلب جديد 🛒🛍️",
                "طلب جديد من العميل🛒🛍️" . $user->name,
                $order->id,
                "order"
            );*/
            // printf($order->invoice);
        }

        $pre_order['total'] = round($pre_order['total'] + $order->delivery_price, 2);

        DB::commit();

        $carts = $this->model
            ->where(['user_id' => Auth::id()])
            ->delete();
        return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $order ? true : false, 200);
    }


}
