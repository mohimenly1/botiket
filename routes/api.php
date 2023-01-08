<?php

use App\Events\BershkaProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\BrandVisitorsController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PercentageController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WishlistItemController;
use App\Models\Banner;
use App\Models\Delivery;
use App\Models\Percentage;

use App\Models\Brand;
use App\Models\BrandVisitors;
use App\Models\Product;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'api'], function () {

    Route::get("test-scraper",function(){
        $product = Product::find("21883");
        BershkaProductRequest::dispatch($product);

        response("asfasfasfas",200);
    });

    Route::POST('/login', [AuthController::class, 'login']);
    Route::POST('/admin/login', [AuthController::class, 'adminLogin']);
    Route::POST('/fall-back-generate-brand-visitors', function () {
        foreach (Brand::all() as $brand) {
            $brand_visitors = new BrandVisitors();
            $brand_visitors->brand_id = $brand->id;
            $brand_visitors->counter = 0;
            $brand_visitors->created_at = Carbon::now()->startOfMonth();
            //gets the date now and gets the last day of this month along with the time
            $brand_visitors->end_date = Carbon::parse(Carbon::now())->endOfMonth()->toDateTimeLocalString();
            $brand_visitors->save();
        }
        return [
            'status' => 'Your operation is successful.'
        ];
    });

    Route::POST('/register', [AuthController::class, 'register']);

    Route::POST('/check-phone', [AuthController::class, 'checkPhone']);
    Route::POST('/reset-password', [AuthController::class, 'resetPassword']);

    //cities
    Route::GET('cities', [CityController::class, 'index']);


    Route::group(['middleware' => 'jwtMiddleware'], function () {
        Route::POST('/logout', [AuthController::class, 'logout']);
        Route::GET('/profile', [AuthController::class, 'profile']);
        Route::GET('/followed-stores', [AuthController::class, 'followedStores']);
        Route::GET('/unfollow-store/{store_id}', [AuthController::class, 'unfollowStore']);
        Route::POST('/update-profile', [AuthController::class, 'updateProfile']);
        Route::POST('/add-address', [AuthController::class, 'addAddress']);
        Route::POST('/update-address', [AuthController::class, 'updateAddress']);
        Route::POST('/delete-address', [AuthController::class, 'deleteAddress']);
        //notifications
        Route::GET('notifications', [NotificationController::class, 'index']);
        //change notification status
        Route::GET('notification/status/{notification}', [NotificationController::class, 'update']);

        Route::GET('update-state', [StoreController::class, 'UpdatState']);


        //super and Store admin routes
        Route::group(['middleware' => 'superStoreAdmin'], function () {

            Route::POST('update_local_rate', [BrandController::class, 'update_local_rate'])->middleware('updatecurrenciesrate');

            Route::GET('statistics', [HomeController::class, 'statistics']);

            //Products
            Route::GET('products/quantities/{products}', [ProductController::class, 'quantities']);
            Route::POST('products/report', [ProductController::class, 'report']);
            Route::GET('products/skus', [ProductController::class, 'skus']);
            Route::GET('products/data', [ProductController::class, 'data']);
            Route::GET('products/brands', [ProductController::class, 'brands']);
            Route::GET('products/colors', [ProductController::class, 'colors']);
            Route::GET('products/genders', [ProductController::class, 'genders']);
            Route::GET('products/offers', [ProductController::class, 'offers']);
            Route::GET('products/main-categories/{gender}', [ProductController::class, 'mainCategories']);
            Route::GET('products/sub-categories/{category}', [ProductController::class, 'subCategories']);
            Route::POST('products/filter', [ProductController::class, 'filter']);
            Route::POST('products/search', [ProductController::class, 'search']);
            Route::POST('products/restore/{products}', [ProductController::class, 'restore']);
            Route::GET('products/archive', [ProductController::class, 'indexDeleted']);
            Route::apiResource('products', ProductController::class);

            //Offers
            Route::apiResource('offers', OfferController::class)->except(['create', 'edit']);

            // Categories

      

            //Wishlist

            Route::POST('wishlist/discount/{product}', [WishlistItemController::class, 'discount']);
            Route::apiResource('wishlist', WishlistItemController::class)->only(['index', 'show']);

            //Collections
            Route::apiResource('collections', CollectionController::class)->only(['store', 'show', 'index', 'update', 'destroy']);

            //Coupons
            Route::apiResource('coupons', CouponController::class)->only(['store', 'index', 'destroy']);

            //deliveries
            Route::GET('deliveries', [DeliveryController::class, 'index']);


            //Orders
            Route::GET('orders/statistics', [OrderController::class, 'statistics']);
            Route::GET('orders/archive', [OrderController::class, 'indexDeleted']);
            Route::POST('orders/restore/{order}', [OrderController::class, 'restore']);
            Route::apiResource('orders', OrderController::class)->except(['create', 'edit']);

            //super admin routes
            Route::group(['middleware' => 'superAdmin'], function () {
                // brand

                Route::POST('/brand/visitors', [BrandVisitorsController::class, 'brandMonthVisitors']);
                Route::GET('update_scrapeed', [ProductController::class, 'scrape']);

                //transactions
                Route::POST('transactions/report', [TransactionController::class, 'report']);
                Route::GET('transactions/statistics', [TransactionController::class, 'statistics']);
                Route::GET('transactions/invoices', [TransactionController::class, 'invoices']);
                Route::apiResource('transactions', TransactionController::class)->except(['create', 'edit', 'destroy']);

                //Class B Products
                Route::POST('products/class-b/index', [ProductController::class, 'classBIndex']);
                Route::POST('products/class-b/store', [ProductController::class, 'classBStore']);

                //Class B Orders
                Route::POST('orders/class-b/index', [OrderController::class, 'classBIndex']);
                Route::POST('orders/class-b/store', [OrderController::class, 'classBStore']);
                Route::PUT('orders/class-b/update/{order}', [OrderController::class, 'classBUpdate']);

                //users
                Route::POST('users/report', [UserController::class, 'report']);
                Route::GET('users/archive', [UserController::class, 'indexDeleted']);
                Route::GET('users/store-admin', [UserController::class, 'indexStoreAdmin']);
                Route::GET('users/archive-store-admin', [UserController::class, 'indexDeletedStoreAdmin']);
                Route::GET('users/get-unpaginated', [UserController::class, 'getUnpaginatedUsers']);
                Route::POST('users/restore/{user}', [UserController::class, 'restore']);
                Route::apiResource('users', UserController::class)->except(['create', 'edit']);

                //brands
                Route::GET('brands/archive', [BrandController::class, 'indexDeleted']);
                Route::POST('brands/restore/{user}', [BrandController::class, 'restore']);
                Route::GET('currencies', [BrandController::class, 'currencies']);
                Route::apiResource('brands', BrandController::class)->except(['create', 'edit']);
                Route::POST('brands/update_percentage', [BrandController::class, 'update_percentage'])->middleware('updatecurrenciesrate');

                Route::prefix("brands/{brand}/percentage")->group(function(){
                    
                    //get all brand percentage
                    Route::get("",[PercentageController::class,"index"]);

                    // add percentage
                    Route::post("",[PercentageController::class,"store"]);


                    Route::prefix("{percentage}")->group(function(){
                        
                        // get by id
                        Route::get("",[PercentageController::class,"show"]);

                        //update brand percentage
                        Route::put("",[PercentageController::class,"update"]);
                    
                        //delete brand percentage
                        Route::delete("",[PercentageController::class,"destroy"]);

                        
                    });

                });

                //deliveries
                Route::POST('deliveries/report', [DeliveryController::class, 'report']);
                Route::POST('deliveries/restore/{user}', [DeliveryController::class, 'restore']);
                Route::GET('deliveries/archive', [DeliveryController::class, 'indexDeleted']);
                Route::apiResource('deliveries', DeliveryController::class)->except(['create', 'edit', 'index']);


                //Stores
                Route::POST('stores/restore/{store}', [StoreController::class, 'restore']);
                Route::GET('stores/archive', [StoreController::class, 'indexDeleted']);
                Route::apiResource('stores', StoreController::class)->except(['create', 'edit']);
                //Banners
                Route::apiResource('banners', BannerController::class)->except(['create', 'edit']);

                //global notifications
                Route::POST('notifications', [NotificationController::class, 'store']);
            });
        });
    });
});



/*
|--------------------------------------------------------------------------
| Application APIS Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



/*----------------------------Store feature-------------------------------------------*/

//Store Toggle Feature View State
Route::GET('store-state', [StoreController::class, 'ShowState']);



//Home page 1
Route::GET('new-of-the-week', [StoreController::class, 'newOfTheWeek']);
Route::GET('main-search/{search}', [StoreController::class, 'mainSearch']);
// Home page / stores
Route::GET('stores/store-of-the-week/{city_id}', [StoreController::class, 'storeOfTheWeek']);

//Store page
Route::GET('stores/with-genders/{store_id}', [StoreController::class, 'withGenders']);
Route::GET('stores/follow/{store_id}', [StoreController::class, 'follow']);

//Store's category products page
Route::GET('stores/categroies/{store_id}/{gender_id}', [StoreController::class, 'categroies']);
Route::GET('stores/subcategories/{store_id}/{gender_id}/{category_id}', [StoreController::class, 'subcategories']);
Route::GET('stores/products/{store_id}/{gender_id}/{category_id}', [StoreController::class, 'products']);

//Home page/ categories
Route::GET('stores/gender-categories/{city_id}/{gender_id}', [StoreController::class, 'GenderCategories']);
Route::GET('stores/brand-genders-categories/{brand_id}', [StoreController::class, 'brandGendersCategories']);
Route::GET('stores/brand-category-products/{brand_id}/{category_id}', [StoreController::class, 'brandCategoryProducts']);

// All Categories for a specific gender - create date : Feb-6th-2022
Route::GET('stores/all-gender-categories/{gender_id}', [StoreController::class, 'AllGenderCategories']);

// All Gender Products create date: Jan-10th-2022
Route::GET('stores/brand-gender-products/{brand_id}/{gender_id}', [StoreController::class, 'brandGenderProducts']);

// All Brand products that belong to a gender and category - create date: Feb-5th-2022
Route::GET('stores/brand-products-gender-category/{category_id}', [StoreController::class, 'brandProductsGenderCategories']);
Route::GET('stores/colors-sizes-category/{category_id}', [StoreController::class, 'colorsSizesForCategory']);
// Filtering products for all brands based on (category, color and size) - create date: Feb-10th-2022
Route::post('/stores/filter-brand-products-category', [StoreController::class, 'filterColorsSizesForCategory']);

//Categories page
Route::GET('stores/city-stores-categories/{city_id}', [StoreController::class, 'cityStoresCtegories']);

//products page
Route::GET('stores/gender-category-products/{cit_id}/{gender_id}/{category_id}', [StoreController::class, 'genderCategoryProducts']);
Route::GET('stores/gender-categories-subcategories/{cit_id}/{gender_id}/{category_id}', [StoreController::class, 'genderCategoriesSubcategories']);

//Products filter page
Route::GET('category-color/{category_id}', [StoreController::class, 'categoryColors']);
Route::GET('category-sizes/{category_id}', [StoreController::class, 'categorySizes']);
Route::GET('category-store-color/{category_id}/{store_id}', [StoreController::class, 'categoryStoreColors']);
Route::GET('category-store-sizes/{category_id}/{store_id}', [StoreController::class, 'categoryStoreSizes']);
Route::GET('brand-color/{brand_id}/{category_id}', [StoreController::class, 'brandColors']);
Route::GET('brand-sizes/{brand_id}/{category_id}', [StoreController::class, 'brandSizes']);
Route::GET('filter-products', [ProductController::class, 'filterProducts']);

//product details page
Route::GET('product-details/{id}', [ProductController::class, 'productDetails']);
Route::GET('sku-product-details/{sks}', [ProductController::class, 'skuProductDetails']);

//bannerBannerss
Route::GET('banners', [BannerController::class, 'index']);

/*---------------------------- Class A feature-------------------------------------------*/
Route::GET('class-a-brands', [BrandController::class, 'indexHasProducts']);
Route::GET('brands/products/{id}', [BrandController::class, 'products']);
Route::GET('brands/shipped_products/all', [BrandController::class, 'product_is_shipped']);



Route::group(['middleware' => ['api', 'jwtMiddleware']], function () {
    /*----------------------------Store feature-------------------------------------------*/


    //Store page
    Route::GET('stores/follow/{store_id}', [StoreController::class, 'follow']);

    /*----------------------------Favorites feature-------------------------------------------*/

    //Favorites lists page
    Route::GET('favorites/products', [FavoriteController::class, 'fsavoriteProducts']);
    Route::POST('favorites/add-product', [FavoriteController::class, 'addProduct']);
    Route::POST('favorites/delete-product', [FavoriteController::class, 'deleteProduct']);
    Route::apiResource('favorites', FavoriteController::class)->except(['create', 'edit']);


    /*----------------------------Order feature-------------------------------------------*/
    Route::GET('user-orders', [OrderController::class, 'userOrders']);
    Route::GET('order-details/{id}', [OrderController::class, 'ordersDetails']);
    Route::GET('cancel-order/{id}', [OrderController::class, 'cancelOrder']);
    Route::GET('confirm-order/{id}', [OrderController::class, 'confermOrder']);

    Route::POST('orders/add-transaction/{id}', [OrderController::class, 'addTransaction']);
    Route::POST('orders/payment-method/{id}', [OrderController::class, 'paymentMethod']);
    /*----------------------------Cart feature-------------------------------------------*/

    //Product details page
    //Add cart item
    //Cart page
    //Get cart items
    //Delete cart item
    //update cart item quantity
    
    Route::POST('cart/pre-order', [CartItemController::class, 'preOrder']);
    Route::apiResource('cart', CartItemController::class)->except(['create', 'edit', 'show']);
    // Route::POST('add-to-cart', [CartItemController::class, 'store']);
    Route::POST('order', [CartItemController::class, 'order']);

    /*---------------------------- Wishlist feature-------------------------------------------*/
    Route::POST('wishlist/{id}', [WishlistItemController::class, 'store']);
    Route::POST('wishlist-to-cart/{id}', [WishlistItemController::class, 'wishlistToCart']);
    Route::POST('wishlist', [WishlistItemController::class, 'destroy']);
    Route::Delete('wishlist/{id}', [WishlistItemController::class, 'destroySinleItem']);
    Route::GET('user-wishlist-items', [WishlistItemController::class, 'userItems']);
    /*---------------------------- add Category feature-------------------------------------------*/
    Route::GET('categories', [CategoryController::class, 'index']);
    Route::POST('add-category', [CategoryController::class, 'store']);
    Route::POST('edit-category/{id}', [CategoryController::class, 'update']);
    Route::DELETE('delete-category/{id}', [CategoryController::class, 'destroy']);

});
