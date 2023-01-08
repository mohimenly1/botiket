<?php

namespace App\Services;

use App\Models\Coupon;
use App\Repositories\CouponRepository;
use Auth;
use Illuminate\Http\Request;

class CouponService
{
    protected $coupon;

    public function __construct(Coupon $coupon)
    {
        $this->coupon = new CouponRepository($coupon);
    }
    /**
       * Display a listing of the resource.
       */
    public function index()
    {
        return $this->coupon->index();
    }
    /**
       * Display a listing of the resource.
       */
    public function indexDeleted()
    {
        return $this->coupon->indexDeleted();
    }
   
    /**
     * Store a newly created resource in storage.
     */
    public function store($request)
    {
        return $this->coupon->store($request);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return $this->coupon->destroy($id);
    }
}
