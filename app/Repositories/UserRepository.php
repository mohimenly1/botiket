<?php

namespace App\Repositories;

use App\Helpers\FileHelper;
use App\Http\Traits\CrudTrait;
use App\Http\Traits\MainTrait;
use App\Http\Traits\ResponseTraits;
use App\Models\BeamsNotification;
use App\Models\Order;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\QueryBuilder;

class UserRepository
{
    use CrudTrait, ResponseTraits, MainTrait;
    /**
     * @var User
     */
    protected $user;

    /**
     * UserRepository constructor.
     *
     * @param User $user
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
    /**
     * Get all users with Role.
     *
     * @return User $user
     */
    public function getWhereRole($role, $deletd, $relation)
    {
        if ($deletd) {
            return QueryBuilder::for($this->model)
            ->allowedFilters(['id', 'name', 'phone','store.name'])
            ->defaultSort('-id')
            ->where(['role' => $role])
            ->with($relation)
            ->allowedFilters(['id', 'name', 'phone','store.name'])
            ->allowedSorts(['id', 'name', 'phone','store_id'])
            ->onlyTrashed()
            ->paginate(10);
        }else{
            return QueryBuilder::for($this->model)
            ->defaultSort('-id')
            ->where(['role' => $role])
            ->with($relation)
            ->allowedFilters(['id', 'name', 'phone','store.name'])
            ->allowedSorts(['id', 'name', 'phone','store_id'])
            ->paginate(10);
        }
    }
    /**
     * Get user by id
     *
     * @param $id
     * @return mixed
     */
    public function showUser($id)
    {
        return $this->showTrait($this->model, $id);
    }
    /**
     * Get user by id
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
        $this->model->password = bcrypt($request->password);
        $this->model->role = 'store-admin';
        $this->model->save();
        if ($request->hasFile('image')) {
            $image_path = $request->file('image')->store('/users/' . $this->model->id , 's3');
            Storage::disk('s3')->setVisibility($image_path, 'public');
            $this->model->image = Storage::disk('s3')->url($image_path);
            $this->model->save();
        }
        if ($request->has('store_id')) {
            $this->model->store()->attach($request->store_id);
        }
        return $this->model->load('store');
    }
    /**
    * Update Profile
    * Store to DB if there are no errors.
    *
    * @param array $data
    * @return String
    */

    public function update($request, $user)
    {
        $user = User::findOrFail($user);
        $user->fill($request->except('password', 'store_id'));
        if ($request->has('password')) {
            $user->password = bcrypt($request->password);
        }
        if ($request->hasFile('image')) {
            $delete_old_image= Storage::disk('s3')->delete($user->image);
            if ($delete_old_image) {
                $image_path = $request->file('image')->store('/users/' . $this->model->id, 's3');
                Storage::disk('s3')->setVisibility($image_path, 'public');
                $user->image = Storage::disk('s3')->url($image_path);
            }
        }
        if ($request->has('store_id')) {
            $user->store()->sync($request->store_id);
        }
        $user->save();

        return $user->load('store');
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
     * Reset Password
     *
     * @param array $data
     * @return String
     */

    public function resetPassword($request)
    {
        $user = User::where('phone', $request->phone)->first();
        $user->password = bcrypt($request->password);
        $user->save();
        $data['message'] = 'success';
        $data['user_type'] = $user->type;
        return $data;
    }

    public function report($request)
    {
        $Orders = Order::orderBy('id', 'asc')
            ->where('user_id', $request->user_id)
            ->where('order_status_id', 5)
            ->whereBetween('delivery_date', [Carbon::parse($request->from_date), Carbon::parse($request->to_date)])
            ->select(['id','delivery_date', 'store_id', 'total_amount'])
            ->with(['store:id,name'])
            ->get();
            $Orders_total_amount = $Orders->pluck('total_amount')->sum();
            return['Orders total amount'=>$Orders_total_amount,'Orders'=>$Orders];
    }
    
}
