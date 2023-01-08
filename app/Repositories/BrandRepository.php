<?php

namespace App\Repositories;

use App\Helpers\FileHelper;
use App\Http\Traits\CrudTrait;
use App\Http\Traits\MainTrait;
use App\Http\Traits\ResponseTraits;
use App\Models\BeamsNotification;
use App\Models\Brand;
use App\Models\Currency;
use App\Models\Product;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BrandRepository
{
    use CrudTrait, ResponseTraits, MainTrait;
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
        return $this->indexTrait($this->model);
    }
    /* Display a listing of the resource if brand have products.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexHasProducts()
    {
        return Brand::all();
    }
    /**
     * Get all brands with Role.
     *
     * @return Delivery $brand
     */
    public function indexDeleted()
    {
        return $this->indexDeletedTrait($this->model);
    }

    public function currencies()
    {
        return Currency::all();
    }
    /**
     * Get brand by id
     *
     * @param $id
     * @return mixed
     */
    public function showUser($id)
    {
        return $this->showTrait($this->model, $id);
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
        DB::beginTransaction();
        $this->model->name = $request->name;
        $this->model->selling_currency_id = $request->selling_currency_id;
        $this->model->original_currency_id = $request->original_currency_id;
        $this->model->increase_percentage = $request->increase_percentage;
        // $this->model->logo = "asfasfasff";
        // return dd($request['logo']);

        if ($request->hasFile('logo')) {
            $image_path = $request->file('logo')->store('/brands' . $this->model->id, 's3');

            Storage::disk('s3')->setVisibility($image_path, 'public');
            $this->model->logo = Storage::disk('s3')->url($image_path);
            $this->model->save();
        }
        $this->model->save();
        DB::commit();
        return $this->model;
    }
    /**
     * Update Profile
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */

    public function update($request, $brand)
    {
        $brand = Brand::findOrFail($brand);
        $brand->name = $request->name ? $request->name : $brand->name;

        //$brand->selling_currency_id=$request->selling_currency_id;
        // $brand->original_currency_id=$request->original_currency_id;

        $brand->increase_percentage = $request->increase_percentage;

        if ($request->hasFile('logo')) {
            $delete_old_image = Storage::disk('s3')->delete($brand->logo);
            if ($delete_old_image) {
                $image_path = $request->file('logo')->store('/brands/' . $this->model->id , 's3');
                Storage::disk('s3')->setVisibility($image_path, 'public');
                $brand->logo = Storage::disk('s3')->url($image_path);
            }
        }

        $brand->save();

        return $brand;
    }



    /**
     * destroy User
     *
     * @param $data
     * @return User
     */
    public function destroy($id)
    {
        $brand = $this->model->find($id);
        $opend_orders = Product::where('brand_id', $brand->id)->whereHas('orderItems', function ($q) {
            $q->whereHas('order', function ($q) {
                $q->whereIn('order_status_id', [1, 2, 3, 4]);
            });
        })->with('orderItems.order')->lazy();

        if (count($opend_orders->toArray()) < 1) {
            if ($brand) {
                $brand->products()->delete();
            }
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $this->destroyTrait($this->model, $id), 200);
        } else {
            $orders_array = [];
            foreach ($opend_orders->pluck('orderItems') as $item) {
                foreach ($item as $order) {
                    $orders_array[] = ($order->order_id);
                }
            };
            return $this->prepare_response(__('auth.Something went wrong'), ('تأكد من اتمام او الغاء هذه الطلبات اولا ' . implode(", ",  array_values(array_unique($orders_array)))), null, 400);
        }
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
     * get products by brand id
     *
     * @param $data
     * @return User
     */
    public function products($id)
    {
        return $this->model->where('id', $id)->first()->products()->with('firstMedia')->orderBy('created_at', 'desc')->paginate(10);
    }

    public function product_is_shipped()
    {
        return Product::where('is_shipped', 1)->where('brand_id', '!=', null)->with('firstMedia')->paginate(10);
    }
}
