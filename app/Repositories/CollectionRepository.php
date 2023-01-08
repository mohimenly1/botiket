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
use App\Models\Collection;
use PhpParser\Node\Stmt\Foreach_;

class CollectionRepository
{
    use CrudTrait, ResponseTraits, MainTrait;
    /**
     * @var Delivery
     */
    protected $collection;

    /**
     * UserRepository constructor.
     *
     * @param Delivery $collection
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
    /**
     * Get all collections with Role.
     *
     * @return Delivery $collection
     */
    public function index()
    {
        $user=Auth::user();
        if ($user->role=='super-admin') {
            $collections= $this->model->select('id', 'description')
            ->whereNull('store_id')
            ->with(['products:id,title','products.firstMedia'])
            ->orderBy('id', 'desc')->get();
        } elseif ($user->role=='store-admin') {
            $collections= $this->model->select('id', 'description')
            ->where('store_id', $user->store()->first()->id)
             ->with(['products:id,title','products.firstMedia'])
             ->orderBy('id', 'desc')->get();
        }
        foreach ($collections as $collection) {
            $first=$collection->products->first();
            if($first) $collection['media'] =$first->toArray()['first_media']['path'];
            unset($collection['products']);

        }
        return $collections;
    }

    /**
     * Get collection by id
     *
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        return $this->model
       ->where('id', $id)
       ->select('id', 'description')
         ->with(['products:id,title,description','products.firstMedia'])
         ->orderBy('id', 'desc')->get();
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

        $this->model->description = $request->description;
        if ($user->role=='super-admin') {
            $this->model->store_id = null;
        } else {
            $this->model->store_id = $user->store()->first()->id;
        }
        $this->model->save();
        if ($request->has('products')) {
            foreach ($request->products as $product) {
                $this->model->products()->attach($product);
            }
        }
        return $this->model->load('products:id,title,sku');
    }
 
    /**
    * Update
    * Store to DB if there are no errors.
    *
    * @param array $data
    * @return String
    */

    public function update($request, $product)
    {
        $product = Collection::findOrFail($product);

        $product->fill($request->all());
        $user=Auth::user();
        if ($user->role=='super-admin') {
            $product->store_id = null;
        } else {
            $product->store_id = $user->store()->first()->id;
        }
        $product->save();
        if ($request->has('products')) {
            $product->products()->sync($request->products);
        }
        return $product->load('products:id,title,sku');
        return $product;
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
