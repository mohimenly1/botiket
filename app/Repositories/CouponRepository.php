<?php

namespace App\Repositories;

use App\Helpers\FileHelper;
use App\Http\Traits\CrudTrait;
use App\Http\Traits\MainTrait;
use App\Http\Traits\ResponseTraits;
use App\Models\BeamsNotification;
use App\Models\Coupon;
use Auth;
use Illuminate\Database\Eloquent\Model;

class CouponRepository
{
    use CrudTrait, ResponseTraits, MainTrait;
    /**
     * @var Delivery
     */
    protected $coupon;

    /**
     * UserRepository constructor.
     *
     * @param Delivery $coupon
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
    /**
     * Get all coupons with Role.
     *
     * @return Delivery $coupon
     */
    public function index()
    {
        $user=Auth::user();
        if ($user->role=='super-admin') {
            return $this->model->select('id', 'code', 'value', 'is_percentage', 'created_at', 'usage_count')->whereNull('store_id')
            ->orderBy('id', 'desc')->get();
        } elseif ($user->role=='store-admin') {
            return $this->model->select('id', 'code', 'value', 'is_percentage', 'created_at', 'usage_count')->where('store_id', $user->store()->first()->id)
            ->orderBy('id', 'desc')->get();
        }
        return $this->indexTrait($this->model);
    }
    /**
     * Get all coupons with Role.
     *
     * @return Delivery $coupon
     */
    public function indexDeleted()
    {
        return $this->indexDeletedTrait($this->model);
    }
    /**
     * Get coupon by id
     *
     * @param $id
     * @return mixed
     */
    public function showUser($id)
    {
        return $this->showTrait($this->model, $id);
    }
    /**
     * Get coupon by id
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
        $user=Auth::user();

        if ($request->has('code')) {
            $this->model->code = $request->code;
            $this->model->is_percentage = $request->is_percentage;
            $this->model->value = $request->value;
            $this->model->usage_count = $request->usage_count;
            if ($user->role=='super-admin') {
                $this->model->store_id = null;
            } else {
                $this->model->store_id = $user->store()->first()->id;
            }
            $this->model->save();
            return $this->model;
        } elseif ($request->has('coupon_count')) {
            $coupons=[];
            for ($i=0;$i<$request->coupon_count;$i++) {
                $coupon=new Coupon;
                $coupon->code = rand(1000000, 99999999);
                $coupon->is_percentage = $request->is_percentage;
                $coupon->value = $request->value;
                $coupon->usage_count = $request->usage_count;
                if ($user->role=='super-admin') {
                    $this->model->store_id = null;
                } else {
                    $coupon->store_id = $user->store()->first()->id;
                }
                $coupon->save();
                $coupons[]=$coupon;
            }
            return $coupons;
        }
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
}
