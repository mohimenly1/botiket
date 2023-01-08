<?php

namespace App\Repositories;

use App\Helpers\FileHelper;
use App\Http\Traits\CrudTrait;
use App\Http\Traits\MainTrait;
use App\Http\Traits\ResponseTraits;
use App\Models\BeamsNotification;
use App\Models\Brand;
use App\Models\CartItem;
// use App\Models\Collection;
use App\Models\Product;
use App\Models\WishlistItem;
use Auth;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\New_;
use App\Http\Traits\NotificationTrait;


class WishlistRepository
{
    use CrudTrait;
    use ResponseTraits;
    use MainTrait;
    use NotificationTrait;

    /**
     * @var Wishlist
     */
    protected $wishlist;

    /**
     * UserRepository constructor.
     *
     * @param Wishlist $wishlist
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
    /**
     * Get all wishlists with Role.
     *
     * @return Wishlist $wishlist
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->role == 'super-admin') {
            $wishlist = $this->model->with('product')->withAndWhereHas(
                'product',
                function ($query) use ($user) {
                    $query->whereNotNull('brand_id')->whereNull('store_id');
                }
            )
                ->orderBy('id', 'desc')->get();
        } elseif ($user->role == 'store-admin') {
            $wishlist = $this->model->with('product')->withAndWhereHas(
                'product',
                function ($query) use ($user) {
                    $query->where('store_id', $user->store()->first()->id)->whereNull('brand_id');
                }
            )->orderBy('id', 'desc')->get();
        }
        $products= [];
        // return $wishlist->pluck('product') ;
        foreach ($wishlist->pluck('product') as $product) {
            $products[]=$product->load('firstMedia', 'category:id,name', 'subCategory:id,name')
                        ->only(['id', 'sku', 'price','offer_id', 'title', 'firstMedia', 'category', 'subCategory']);
            
        }
        return ($products);
    }

    /**
     * Get wishlist by id
     *
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $wish = $this->model->select('discount', 'user_id')->where('product_id', $id)->with('user:id,name,phone,image')->get();
        return $wish;
    }
    /**
     * Get wishlist by id
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

    public function store($id)
    {
        $product = Product::findOrFail($id);
        return Auth::user()->WishlistItems()->where(['product_id' => $product->id])->firstOrCreate(['product_id' => $product->id]);
    }
    /**
     * add wishlist item to cart.
     */
    public function wishlistToCart($request, $id)
    {
        $wish = $this->model->where('product_id', $id)->first();
        if ($wish) {
            $wishArray = $wish->toArray();
            $wishArray['quantity_id'] = $request->quantity_id;
            $cart =  CartItem::firstOrCreate($wishArray);
            $wish->delete();
            return $cart;
        } else {
            return false;
        }
    }

    /**
     * Update Profile
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */


    /**
     * destroy User
     *
     * @param $data
     * @return User
     */
    public function destroy($request)
    {
        $destroy = $this->model->whereIn('product_id', $request->item_id)->delete();
        if ($destroy > 0) {
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $destroy, 200);
        } else {
            return $this->prepare_response($destroy, __('auth.Something went wrong'), null, 400);
        }
    }
    public function destroySinleItem($id)
    {
        return  $this->model->where('product_id', $id)->delete();
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
     * Add discount
     *
     * @param $data
     * @return User
     */
    public function discount($request, $product)
    {
        $WishlistItem = WishlistItem::whereIn('user_id', $request->users)
            ->where('product_id', $product)
            ->update(array('discount' => $request->discount_value));
        $productName = Product::findOrFail($product);
        $storeName = 'Boutiquette';
        if($productName->store){
            $storeName = $productName->store->name;
        }
        

        $this->send(
            Auth::id(),
            $request->users,
            "تخفيض جديد لقائمة الأمنيات",
            " تم إعطائك تخفيض لقائمة الأمنيات على منتج ". $productName->title . " من قبل متجر ". $storeName,
            $productName->id,
            "transaction"
        );
        return $WishlistItem;

    }
    /**
     * get wishlist items.
     */
    public function userItems()
    {
        $items = $this->model->where('user_id', Auth::id())->with('product:id,title,price,offer_id', 'product.firstMedia', 'product.quantities')->get();
        $new_items = [];
        foreach ($items as $item) {
            $quantities = $item->product->quantities()
                ->with('color')
                ->orderBy('id', 'desc')
                ->get()
                ->groupBy('color.id');
            $quantitiesBySize=$item->product->quantities()
                ->with('color')
                ->orderBy('id', 'desc')
                ->get()
                ->groupBy('size');
            $newquantities  = $size_quantities=[];
            $i = $j=0;
            foreach ($quantities as $key => $quantity) {
                $newquantities[$i]["id"] = $quantity[0]['color']['id'];
                $newquantities[$i]["name"] = $quantity[0]['color']['name'];
                $newquantities[$i]["color_value"] = $quantity[0]['color']['color_value'];
                foreach ($quantity as $quantityitem) {
                    $newquantities[$i]['quantities'][] = $quantityitem->only(['id', 'size', 'quantity']);
                }
                $i++;
            }

            foreach ($quantitiesBySize as $key => $quantity) {
                $size_quantities[$j]["size"] = $quantity[0]['size'];
                 foreach ($quantity as $quantityitem) {
                    $size_quantities[$j]['quantities'][] =array_merge($quantityitem->color->only(['id', 'name', 'color_value']),$quantityitem->only(['quantity']));
                }
                $j++;
            }
            unset($item['color']);
            $product = $item['product'];

            unset($product['quantities']);
            $product['quantities'] = $newquantities;
            $product['size_quantities'] = $size_quantities;
            unset($item['product']);
            $item['product'] = $product;
            $new_items[] = $item;
        }
        return ($new_items);
    }
}
