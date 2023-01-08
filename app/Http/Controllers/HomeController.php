<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Store;
use App\Models\Brand;
use App\Models\Invoices;
use App\Http\Traits\ResponseTraits;
use App\Models\BrandVisitors;
use Carbon\Carbon;

class HomeController extends Controller
{
    use ResponseTraits;
    /**
     * Return back the count for all the Home Page w .
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function statistics()
    {
        try {
            $user = Auth::user();
            if ($user->role == 'super-admin') {
                $brands = Brand::all()->count();
                $stores = Store::all()->count();
                $users = User::where('role', 'end-user')->count();
                $products = Product::whereNull('store_id')->whereNotNull('brand_id')->count();
                $sales = Order::whereNull('store_id')->where('order_status_id', 5)->sum('total_amount');

                $brand_visitors = BrandVisitors::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                    ->orderBy('counter', 'DESC')->take(5)->with('brand')->get();
                $total_visitors = BrandVisitors::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum('counter');

                $statistics = [
                    'sales' => $sales, 'products' => $products, 'users' => $users, 'stores' => $stores, 'brands' => $brands,
                    'brandVisitors' => [
                        'top' => $brand_visitors,
                        'total' => $total_visitors
                    ]
                ];
            } elseif ($user->role == 'store-admin') {
                $products = Product::where('store_id', $user->store()->first()->id)
                    ->whereNull('brand_id')
                    ->count();
                $sales = Order::where('store_id', $user->store()->first()->id)
                    ->where('order_status_id', 5)
                    ->sum('total_amount');
                $statistics = ['sales' => $sales, 'products' => $products];
            }

            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $statistics, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
}
