<?php

namespace App\Repositories;

use App\Helpers\FileHelper;
use App\Http\Traits\CrudTrait;
use App\Http\Traits\MainTrait;
use App\Http\Traits\NotificationTrait;
use App\Http\Traits\ResponseTraits;
use App\Models\BeamsNotification;
use App\Models\Coupon;
use App\Models\Currency;
use App\Models\Quantity;
use App\Models\Invoices;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\OrderStatu;
use App\Models\Transaction;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Facades\JWTAuth;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Sorts\Sort;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Foreach_;

class OrderRepository
{
    use CrudTrait, ResponseTraits, MainTrait, NotificationTrait;
    /**
     * @var Delivery
     */
    protected $Order;

    /**
     * UserRepository constructor.
     *
     * @param Delivery $Order
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
    /**
     * Get all Orders with Role.
     *
     * @return Delivery $Order
     */
    public function index()
    {
        // dd(Request()->sort);
        $user = Auth::user();
        if ($user->role == 'super-admin') {
            return QueryBuilder::for($this->model)
                ->defaultSort('-id')
                ->select('order_status_id', 'id', 'user_id', 'store_id', 'total_amount', 'delivery_date', "created_at", 'delivery_id', 'address_id')
                ->allowedIncludes(['address.city'])
                ->whereNull('store_id')
                ->with(['statu:id,status', 'delivery:id,name', 'user:id,name', 'address.city'])
                ->allowedSorts(['total_amount', 'delivery_date', 'order_status_id'])
                ->groupBy('order_status_id', 'id')
                ->allowedFilters(['id', 'user.name', 'order_status_id'])
                ->paginate(10);
        } elseif ($user->role == 'store-admin') {
            return QueryBuilder::for($this->model)
                ->defaultSort('-id')
                ->select('id', 'user_id', 'store_id', 'order_status_id', 'total_amount', 'delivery_date', 'delivery_id')
                ->where('store_id', $user->store()->first()->id)
                ->with(['statu:status', 'delivery:name', 'user:name'])
                ->allowedFilters(['id', 'user.name', 'statu.status'])
                ->allowedSorts(['total_amount', 'delivery_date', 'order_status_id'])
                ->paginate(10);
        }
    }
    /**
     * Get all Orders with Role.
     *
     * @return Delivery $Order
     */
    public function indexDeleted()
    {
        $user = Auth::user();
        if ($user->role == 'super-admin') {
            return QueryBuilder::for($this->model)
                ->select('id', 'user_id', 'store_id', 'order_status_id', 'total_amount', 'delivery_date', 'delivery_id')
                ->whereNull('store_id')
                ->with(['statu', 'delivery', 'user'])
                ->defaultSort('-id')
                ->onlyTrashed()
                ->allowedFilters(['id', 'user.name', 'statu.status'])
                ->allowedSorts(['total_amount', 'delivery_date', 'order_status_id'])
                ->paginate(10);
        } elseif ($user->role == 'store-admin') {
            return QueryBuilder::for($this->model)
                ->select('id', 'user_id', 'store_id', 'order_status_id', 'total_amount', 'delivery_date', 'delivery_id')
                ->where('store_id', $user->store()->first()->id)
                ->with(['statu:status', 'delivery:name', 'user:name'])
                ->defaultSort('-id')
                ->onlyTrashed()
                ->allowedFilters(['id', 'user.name', 'statu.status'])
                ->allowedSorts(['total_amount', 'delivery_date', 'order_status_id'])
                ->paginate(10);
        }
    }
    /**
     * Get Order by id
     *
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        return $this->showWithRelationTrait(
            $this->model,
            $id,
            [
                'items.product.firstMedia',
                'items.quantityDetails.color',
                'user:id,name,phone',
                'user.address.city',
                'invoice:id,order_id,coupon_id,discount,payment_method_id',
                'invoice.coupon', 'invoice.paymentMethod:id,name',
                'address:id,city_id,title,longitude,latitude,description',
                'address.city'
            ]
        );
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
        DB::beginTransaction();
        $user = Auth::user();

        if ($user->role == 'store-admin') {
            $request['store_id'] = $user->store()->first()->id;
        }

        $this->model->fill($request->all());
        $this->model->save();


        ////////////////////////////////////////////////////////////////////////////
        if ($request->has('products')) {
            $total_amount = [];
            $discount = $old_amount = 0;


            foreach ($request->products as $product => $data) {
                $new_price = Product::where('id', $product)->first()->new_price;
                $product_price = $new_price ? $new_price : Product::where('id', $product)->first()->price;

                $user_cart_products = User::where('id', $request->user_id)->first()->cartproducts()->where('product_id', $product)->pluck('discount')->first();
                $price =  $product_price;

                if ($user_cart_products) {
                    $price = $user_cart_products != null ? $user_cart_products : $product_price;
                }

                $this->model->items()->create([
                    'product_id' => $product,
                    'quantity' => $data['quantity'],
                    'quantity_id' => $data['quantity_id'],
                    'total_amount' => $data['quantity'] * $price
                ]);

                //if whishlist price exsit then convert it and assign it to the $product_price_usd
                $product_price_usd = $user_cart_products ? ($user_cart_products * Product::find($product)->currency->rate) : Product::find($product)->price_usd;
                $product_old_price_usd = Product::find($product)->old_price_usd;

                $total_amount[] = $data['quantity'] * $product_price_usd;
                $discount += (($product_old_price_usd - $product_price_usd) * $data['quantity']);

                $old_amount += $product_price_usd * $data['quantity'];
            }
        }

        ///////////////////////////////////////////////////////////////////////////
        $local_currency = Currency::find(69);
        $total_price = array_sum($total_amount);
        //return $total_price;

        if ($request->has('coupon_code')) {
            $coupon = Coupon::where('code', $request->coupon_code)->first();
            if ($coupon->usage_count > 0) {
                if ($coupon->is_percentage == 1) {
                    $discount += ($total_price * $coupon->value / 100);
                    $total_price -= ($total_price * $coupon->value / 100);
                } else {
                    $total_price -= ($coupon->value * $local_currency->rate);;
                    $discount += ($coupon->value * $local_currency->rate);;
                }
                $request['coupon_id'] = $coupon->id;
            }
            $coupon->usage_count--;
            $coupon->save();
        }

        if ($request->has('discount')) {
            $total_price -= $request->discount * $local_currency->rate;
            $discount += $request->discount * $local_currency->rate;
        }

        $this->model->old_amount = ceil($old_amount * $local_currency->reverse_rate);


        $this->model->total_amount =  ceil($total_price * $local_currency->reverse_rate);
        $this->model->save();

        $request['rest_amount'] = ceil($this->model->total_amount);
        $request['discount'] = intval($discount * $local_currency->reverse_rate);

        $this->model->invoice()->create($request->all());
        DB::commit();
        return $this->model->id;
    }
    /**
     * Update Profile
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */

    public function update($request, $Order)
    {

        $local_currency = Currency::find(69);
        $Order = Order::findOrFail($Order);
        $request['store_id'] = $Order->store_id;
        $Order->fill($request->all());
        $Order->save();

        if ($request->has('products')) {
            foreach ($request->products as $product => $data) {
                $old_product = $Order->items()->where('product_id', $product)->first();
                if ($data == 0 && $old_product) {
                    // $Order->total_amount -= $old_product->price;
                    $old_product->delete();
                } elseif ($data != 0) {
                    $new_price = Product::where('id', $product)->first()->new_price;

                    $product_price = $new_price ? $new_price : Product::where('id', $product)->first()->price;

                    $user_cart_products = User::where('id', $Order->user_id)->first()->cartproducts()->where('product_id', $product)->pluck('discount')->first();

                    $price = $user_cart_products ? $user_cart_products : $product_price;

                    if ($old_product != null) {
                        $Order->items()->where('product_id', $product)->update([
                            'product_id' => $product,
                            'quantity' => $data['quantity'],
                            'quantity_id' => $data['quantity_id'],
                            'total_amount' => $data['quantity'] * $price
                        ]);
                    } elseif ($old_product == null) {
                        $Order->items()->create([
                            'product_id' => $product,
                            'quantity' => $data['quantity'],
                            'quantity_id' => $data['quantity_id'],
                            'total_amount' => $data['quantity'] * $price
                        ]);
                    }
                }
            }
        }

        $Order->save();
        $total_amount = [];
        $discount = $old_amount = 0;

        foreach ($Order->items as $item) {
            $discount = 0;
            $old_amount = 0;


            // Price Decision START
            $new_price = Product::where('id', $item->product_id)->first()->new_price;
            $product_price = $new_price ? $new_price : Product::where('id', $item->product_id)->first()->price;

            $user_cart_products = User::where('id', $Order->user_id)->first()->cartproducts()->where('product_id', $item->product_id)->pluck('discount')->first();
            $price = $user_cart_products ? $user_cart_products : $product_price;
            // Price Decision END

            //if whishlist price exsit then convert it and assign it to the $product_price_usd
            $product_price_usd = $user_cart_products ? $user_cart_products * Product::find($item->product_id)->currency->rate : Product::find($item->product_id)->price_usd;
            $product_old_price_usd = Product::find($item->product_id)->old_price_usd;

            $total_amount[] = ($item->quantity * $product_price_usd);

            $old_amount += ($product_old_price_usd *  $item->quantity);

            $discount += ($product_old_price_usd - $product_price_usd) *  $item->quantity;
        }

        //in USD
        $total_price = array_sum($total_amount);
        $coupon = null;
        if ($request->has('coupon_code')) {
            $coupon = Coupon::where('code', $request->coupon_code)->first();
            if ($coupon->usage_count > 0 || $coupon->id == $Order->invoice->coupon_id) {

                if ($coupon->is_percentage == 1) {
                    // dd($total_price,$coupon->value,100);
                    $discount += ($total_price * $coupon->value / 100);
                    $total_price -= ($total_price * $coupon->value / 100);
                } else {
                    //conveting the coupon to usd before substracting it from the $total_price
                    $total_price -= ($coupon->value * $local_currency->rate);
                    //conveting the coupon to usd before adding it to the $discount
                    $discount += ($coupon->value * $local_currency->rate);
                }

                if ($coupon->id != $Order->invoice->coupon_id) {
                    $coupon->usage_count--;
                    $coupon->save();
                }

                $Order->total_amount = $total_price;
            }
        }

        if ($request->has('discount')) {
            $total_price -= $request->discount;
            //converting the request discount to usd then adding it to the $discount
            $discount += ceil($request->discount * $local_currency->rate);

            //converting the $discount to lyd then updaing the invoice with this amount
            $invoice = $Order->invoice()->update(['discount' => ceil($discount * $local_currency->reverse_rate)]);
        }

        $invoice = $Order->invoice()->update(
            [
                'discount' => intval($discount * $local_currency->reverse_rate),
                'coupon_id' => $coupon ? $coupon->id : null
            ]
        );

        //the rest_amount needs to be updated
        $Order->invoice()->update(['rest_amount' => ceil($total_price * $local_currency->reverse_rate)]);


        $Order->old_amount = ceil($old_amount * $local_currency->reverse_rate);
        $Order->total_amount = ceil($total_price * $local_currency->reverse_rate);

        $Order->save();
        $this->notification($Order);
        // dd('');

        return $Order->id;
    }

    public function notification($Order)
    {
        if ($Order->store_id) {
            $sender = $Order->store->users()->get()->first()->id;
        } else {
            $sender = Auth::id();
        }
        $title = $body = null;
        if ($Order->order_status_id == 7) {
            $title = __('notifications.order_rejected');
            $body = __('notifications.order_rejected_body');
        } elseif ($Order->order_status_id == 4) {
            $title = __('notifications.order_delivering');
            $body = __('notifications.order_delivering_body');
        } elseif ($Order->order_status_id == 5) {
            $title = __('notifications.order_delived');
            $body = __('notifications.order_delived_body');
        } elseif ($Order->order_status_id == 3) {
            $title = __('notifications.order_accepted');
            $body = __('notifications.order_accepted_body');
        } elseif ($Order->order_status_id == 2) {
            $title = __('notifications.order_on_hold');
            $body = __('notifications.order_on_hold_body');
        } else {
            $title = 'تم تعديل الطلب';
            $body = "تم تعديل طلبك يرجى التحقق";
        }

        $this->send(
            $sender,
            [$Order->user_id],
            $title,
            $body,
            $Order->id,
            "order"
        );
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
    /**
     * Return back the count for all the orders with each statue.
     */
    public function statistics()
    {
        $user = Auth::user();
        if ($user->role == 'super-admin') {
            $orders = $this->model
                ->whereNull('store_id')
                ->select('order_status_id', DB::raw('count(*) as count'))
                ->with('statu:id,status')
                ->groupBy('order_status_id')
                ->get();
        } elseif ($user->role == 'store-admin') {
            $orders = $this->model
                ->where('store_id', $user->store()->first()->id)
                ->select('order_status_id', DB::raw('count(*) as count'))
                ->with('statu:id,status')
                ->groupBy('order_status_id')
                ->get();
        }

        $statistics = [];
        $i = 0;
        $order_status = OrderStatu::get(['id', 'status'])->toArray();
        foreach ($order_status as $order_statu) {
            $order = $orders->where('order_status_id', $order_statu['id'])->first();
            if ($order) {
                $statistics[$i]['id'] =  $order_statu['id'];
                $statistics[$i]['statue'] = $order_statu['status'];
                $statistics[$i]['count'] = $order->count;
            } else {
                $statistics[$i]['id'] =  $order_statu['id'];
                $statistics[$i]['statue'] = $order_statu['status'];
                $statistics[$i]['count'] = 0;
            }
            $i++;
        }
        return $statistics;
    }


    /************************** Class B **********************************/

    /**
     * Display a listing of the resource.
     */
    public function classBIndex($request)
    {
        $startDate = Carbon::parse($request->from);
        $endDate = Carbon::parse($request->to);
        $index = QueryBuilder::for($this->model)
            ->defaultSort('-id')
            ->where('store_id', $request->store_id)
            ->select(['id', 'user_id', 'store_id', 'order_status_id', 'total_amount', 'delivery_date', 'delivery_id']);
        if ($request->has('from') && $request->has('to')) {
            $index = $index->whereBetween('created_at', [$startDate, $endDate]);
        }

        $index = $index->with(['statu:id,status', 'delivery:id,name', 'user:id,name'])
            ->allowedFilters(['id', 'user.name', 'statu.status'])
            ->allowedSorts(['total_amount', 'delivery_date', 'order_status_id'])
            ->paginate(10);
        return $index;
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function classBStore($request)
    // {
    //     $this->model->sku = $request->sku;
    //     $this->model->title = $request->title;
    //     $this->model->description = $request->description;
    //     $this->model->price = $request->price;
    //     $this->model->is_shipped = $request->is_shipped;
    //     $this->model->is_featured = $request->is_featured;
    //     $this->model->brand_id = $request->brand_id;
    //     $this->model->sub_category_id = $request->sub_category_id;
    //     $this->model->category_id = $request->category_id;
    //     $this->model->gender_id = $request->gender_id;
    //     $this->model->offer_id = $request->offer_id;
    //     $this->model->store_id = $request->store_id;
    //     $this->model->save();
    //     if ($request->has('medias')) {
    //         foreach ($request->medias as $media) {
    //             $image_path = FileHelper::upload_file('/products/' . $this->model->id  . '/', $media);
    //             $this->model->medias()->create(['path' => $image_path]);
    //         }
    //     }

    //     if ($request->has('quantities')) {
    //         foreach ($request->quantities as $quantity) {
    //             $this->model->quantities()->create([
    //                 'size' => $quantity['size'],
    //                 'color_id' => $quantity['color_id'],
    //                 'quantity' => $quantity['quantity'],
    //             ]);
    //         }
    //     }
    //     $this->model->save();

    //     return $this->model->id;
    // }


    /**
     * Update the specified resource in storage.
     */
    public function classBUpdate($request, $Order)
    {
        $Order = Order::findOrFail($Order);
        $user = Auth::user();
        $request['store_id'] = $Order->store_id;
        $Order->fill($request->all());
        $Order->save();

        if ($request->has('products')) {
            foreach ($request->products as $product => $data) {
                $old_product = $Order->items()->where('product_id', $product)->first();
                if ($data == 0 && $old_product) {
                    // $Order->total_amount -= $old_product->price;
                    $old_product->delete();
                } elseif ($data != 0) {
                    $product_price = Product::where('id', $product)->pluck('price')->first();
                    $user_cart_products = User::where('id', $Order->user_id)->first()->cartproducts()->where('product_id', $product)->pluck('discount')->first();

                    $price = $user_cart_products ? $user_cart_products : $product_price;

                    if ($old_product != null) {
                        $Order->items()->where('product_id', $product)->update([
                            'product_id' => $product,
                            'quantity' => $data['quantity'],
                            'quantity_id' => $data['quantity_id'],
                            'total_amount' => $data['quantity'] * $price
                        ]);
                    } elseif ($old_product == null) {
                        $Order->items()->create([
                            'product_id' => $product,
                            'quantity' => $data['quantity'],
                            'quantity_id' => $data['quantity_id'],
                            'total_amount' => $data['quantity'] * $price
                        ]);
                    }
                }
            }
        }
        $Order->save();
        $total_amount = [];
        $discount = $old_amount = 0;

        foreach ($Order->items as $item) {
            $discount = 0;
            $old_amount = 0;
            $product_price = Product::where('id', $item->product_id)->pluck('price')->first();
            $user_cart_products = User::where('id', $Order->user_id)->first()->cartproducts()->where('product_id', $item->product_id)->pluck('discount')->first();
            $price = $user_cart_products ? $user_cart_products : $product_price;
            $total_amount[] = ($item->quantity * $price);
            $discount += ($product_price - $price) *  $item->quantity;

            $old_amount += ($product_price *  $item->quantity);
        }
        $total_price = array_sum($total_amount);

        if ($request->has('coupon_code')) {
            $coupon = Coupon::where('code', $request->coupon_code)->first();
            if ($coupon->usage_count > 0) {
                if ($coupon->is_percentage == 1) {
                    // dd($total_price,$coupon->value,100);
                    $discount += ($total_price * $coupon->value / 100);
                    $total_price -= ($total_price * $coupon->value / 100);
                } else {
                    $total_price -= $coupon->value;
                    $discount += $coupon->value;
                }
            }
            $coupon->usage_count--;
            $coupon->save();
        }
        if ($request->has('discount')) {
            $total_price -= $request->discount;
            $discount += $request->discount;
        }
        $invoice = $Order->invoice()->update(['discount' => $discount]);
        $Order->old_amount = $old_amount;
        $Order->total_amount = $total_price;
        $Order->save();


        return $Order->id;
    }



    /**************************Application APIS**********************************/

    /**
     * get all orders.
     */
    public function userOrders()
    {
        $newOrders = [];
        $orders = Auth::user()
            ->orders()
            ->with(['statu', 'firstItem'])
            ->orderBy('id', 'desc')
            ->get(['id', 'order_status_id', 'total_amount', 'created_at']);


        // dd($orders);
        foreach ($orders as $order) {
            $order['items_quantity'] = $order->items()->sum('quantity');
            if (count($order['firstItem']) > 0) {
                $order['image'] = $order['firstItem']->first()->firstMedia;
            } else {
                $order['image'] = null;
            }
            unset($order['firstItem']);
        }
        // dd($orders);
        return $orders;
    }

    /**
     * get order details
     */
    public function ordersDetails($id)
    {
        $Order = Order::where('id', $id)
            ->with([
                'items.product.firstMedia',
                'items.product:id,title',
                'invoice.transactions',
                'address.city',
                'invoice.paymentMethod:id,name',
                'invoice.coupon:id,value,is_percentage,usage_count',
                'statu:id,status',
                'store:id,name',
                'delivery:id,name'
            ])->first();
        if ($Order->coupon_id != null) {
            if ($Order->invoice->coupon->is_percentage == 1) {
                $Order['coupon_value'] = $Order->invoice->coupon->value * $Order->total_amount / 100;
            } else {
                $Order['coupon_value'] = $Order->invoice->coupon->value;
            }
        } else {
            $Order['coupon_value'] = null;
        }
        $Order['company_number'] = 123456;
        return $Order;
    }
    /**
     * cancels the order if its status is "تم التعليق" or "قيد المراجعة
     */
    public function cancelOrder($id)
    {
        $Order = Order::where('id', $id)->first();
        if ($Order->order_status_id == 1 || $Order->order_status_id == 2) {
            $Order->fill(['order_status_id' => 6]);
            // if ($Order->store_id) {
            //     $receivers[] = $Order->store->users()->get()->first()->id;
            // } else {
            //     $receivers = User::where('role', 'super-admin')->pluck('id')->toArray();
            // }
            //  $this->send(
            //     $Order->user_id,
            //     $receivers,
            //     "طلب ملغي 🛒",
            //     "  تم الغاء الطلب من العميل " . Auth::user()->name,
            //     $Order->id,
            //     "order"
            // );
            return $Order->save();
        } else {
            return false;
        }
    }
    /**
     * confirms the order if its status is "تم التعليق"
     */
    public function confermOrder($id)
    {
        $Order = Order::where('id', $id)->first();
        if ($Order->order_status_id == 2) {
            $Order->fill(['order_status_id' => 3]);
            $Order->save();
            return true;
        } else {
            return false;
        }
    }
    /**
     * adds transaction to an order only if its status is "قيد التجهيز
     *
     */
    public function addTransaction($request, $id)
    {
        $invoice_id = Invoices::where('order_id', $id)->pluck('id');
        $Order = Order::where('id', $id)->first();
        $date = Carbon::now();

        if ($Order->order_status_id == 3) {
            $transaction = Transaction::create([
                'invoice_id' => $invoice_id[0],
                'paid_amount' => $request->paid_amount,
                'reference_number' => $request->reference_number,
                'date' => $date->format('Y-m-d'),
            ]);
            if ($Order->store_id) {
                $receivers[] = $Order->store->users()->get()->first()->id;
            } else {
                $receivers = User::where('role', 'super-admin')->pluck('id')->toArray();
            }
            $this->send(
                $Order->user_id,
                $receivers,
                "معاملة جديدة",
                "قام المستخدم " . Auth::user()->name . "بإضافة معاملة مالية إلى طلبه",
                $transaction->id,
                "transaction"
            );


            return $transaction;
        } else {
            return false;
        }
    }

    /**
     * changes the order payment method only if its status is "قيد المراجعة"
     */
    public function paymentMethod($request, $id)
    {
        $invoice = Invoices::where('order_id', $id)->first();
        $Order = Order::where('id', $id)->first();
        if ($Order->order_status_id == 1) {
            $invoice->fill(['payment_method_id' => $request->payment_method_id]);
            return $invoice->save();
        } else {
            return false;
        }
    }
}