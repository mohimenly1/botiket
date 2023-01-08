<?php

namespace App\Repositories;

use App\Helpers\FileHelper;
use App\Http\Traits\CrudTrait;
use App\Http\Traits\MainTrait;
use App\Http\Traits\ResponseTraits;
use App\Models\BeamsNotification;
use App\Models\Delivery;
use App\Models\Order;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;

class DeliveryRepository
{
    use CrudTrait, ResponseTraits, MainTrait;
    /**
     * @var Delivery
     */
    protected $delivery;

    /**
     * UserRepository constructor.
     *
     * @param Delivery $delivery
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
    /**
     * Get all deliverys with Role.
     *
     * @return Delivery $delivery
     */
    public function index()
    {
        return $this->indexTrait($this->model);
    }
    /**
     * Get all deliverys with Role.
     *
     * @return Delivery $delivery
     */
    public function indexDeleted()
    {
        return $this->indexDeletedTrait($this->model);
    }
    /**
     * Get delivery by id
     *
     * @param $id
     * @return mixed
     */
    public function showUser($id)
    {
        return $this->showTrait($this->model, $id);
    }
    /**
     * Get delivery by id
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
        $this->model->name = $request->name;
        $this->model->phone = $request->phone;

        $this->model->save();

        return $this->model;
    }
    /**
     * Update Profile
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */

    public function update($request, $delivery)
    {
        $delivery = Delivery::findOrFail($delivery);

        $delivery->fill($request->all());

        $delivery->save();

        return $delivery;
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
    public function report($request)
    {
        $Orders = Order::orderBy('id', 'asc')
            ->where('delivery_id', $request->delivery_id)
            ->where('order_status_id', 5)
            ->whereBetween('delivery_date', [Carbon::parse($request->from_date), Carbon::parse($request->to_date)])
            ->select(['id', 'delivery_id', 'delivery_date', 'created_at', 'delivery_price', 'store_id', 'total_amount'])
            ->with(['store:id,name'])
            ->get();
            $Orders_total_amount = $Orders->pluck('total_amount')->sum();
            $Orders_delivery_price = $Orders->pluck('delivery_price')->sum();
            return['Orders total amount'=>$Orders_total_amount,'Orders delivery price'=>$Orders_delivery_price,'Orders'=>$Orders];
    }
}
