<?php

namespace App\Repositories;

use App\Helpers\FileHelper;
use App\Http\Traits\CrudTrait;
use App\Http\Traits\MainTrait;
use App\Http\Traits\ResponseTraits;
use App\Models\BeamsNotification;
use App\Models\Offer;
use App\Models\Product;
use App\Models\User;

use Auth;
use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\NotificationTrait;


class OfferRepository
{
    use CrudTrait;
    use NotificationTrait;
    use ResponseTraits;
    use MainTrait;

    /**
     * @var Offer
     */
    protected $offer;

    /**
     * UserRepository constructor.
     *
     * @param Offer $offer
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
    /**
     * Get all offers with Role.
     *
     * @return Offer $offer
     */
    public function index()
    {
        $user=Auth::user();
        if ($user->role=='super-admin') {
            return $this->model
            ->select('id', 'name', 'value', 'is_percentage', 'expire_date')
            ->whereNull('store_id')
            ->withCount('products')
            ->orderBy('id', 'desc')
            ->get();
        } elseif ($user->role=='store-admin') {
            return $this->model
            ->select('id', 'name', 'value', 'is_percentage', 'expire_date')
            ->where('store_id', $user->store()->first()->id)
            ->withCount('products')
            ->orderBy('id', 'desc')
            ->get();
        }
    }
  
    /**
     * Get offer by id
     *
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        return $this->showWithRelationTrait($this->model, $id, ['products:id,title,sku,offer_id']);
    }
   
    /**
     *  Validate User And Provider data.
     * Offer to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */

    public function store($request)
    {
        $user=Auth::user();
        $this->model->name = $request->name;
        $this->model->is_percentage= $request->is_percentage;
        $this->model->value= $request->value;
        $this->model->expire_date = $request->expire_date;
        if ($user->role=='super-admin') {
            $this->model->store_id =null;
        } elseif ($user->role=='store-admin') {
            $this->model->store_id =$user->store()->first()->id;
        }
        $this->model->save();
        if ($user->role=='store-admin') {
            $this->model->store()->where('id', $user->store()->first()->id)->update(['has_sales'=>1]);
        }
        if ($request->has('products')) {
            foreach ($request->products as $product) {
                Product::find($product)->update(['offer_id'=> $this->model->id]);
            }
        }

        if ($this->model->store_id ==null) {
            $receivers[] = User::where('role','end-user')->pluck('id');
        } else {
            $receivers = $this->model->store->followrs->pluck('id');

        }
        if ($this->model->is_percentage == 1) {
            if($user->role=='store-admin'){
                $store = $this->model->store->name;
            }else{
                $store = 'class a';
            }
            $messege = $this->model->value." % ";
            $store_name=" قام متجر ".$store." بعرض " .$messege ."على بعض منتجاته";

        } else {
            $messege =  $this->model->value." دينار ";
            $store_name="هناك عرض ".$messege."علي بعض منتجاتنا";
        }
        $this->send(
            (int)Auth::id(),
            $receivers[0],
            " عرض جديد",
            $store_name,
             $this->model->id,
            "offer"
        );

        return $this->model->id;
    }
    /**
    * Update Profile
    * Offer to DB if there are no errors.
    *
    * @param array $data
    * @return String
    */

    public function update($request, $offer)
    {
        $offer = Offer::findOrFail($offer);
        $offer->fill($request->all());
        if ($request->has('products')) {
            foreach ($request->products as $key=>$product) {
                if (in_array($key, $offer->products->pluck('id')->toArray()) && $product==null) {
                    Product::find($key)->update(['offer_id'=>null]);
                } else {
                    Product::find($product)->update(['offer_id'=> $offer->id]);
                }
            }
        }
        $offer->save();
        return $offer->id;
    }
    /**
     * destroy User
     *
     * @param $data
     * @return User
     */
    public function destroy($id)
    {
        Offer::find($id)->products()->update(['offer_id'=> null]);
        return $this->destroyTrait($this->model, $id);
    }
}
