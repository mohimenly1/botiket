<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\AddAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Requests\UpdateStoreAdminProfileRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\ResponseTraits;
use Illuminate\Support\Facades\Hash;
use App\Helpers\FileHelper;
use App\Http\Requests\DeleteAddressRequest;
use App\Models\Address;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    use ResponseTraits;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('phone', 'password', 'fcm_token');
        $validator = Validator::make($credentials, [
            'phone' => 'required|numeric',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return $this->prepare_response($validator->errors(), __('validation.failed'), null, 400);
        }
        try {
            if (!$token = JWTAuth::attempt(['phone' => $request->phone, 'password' => $request->password])) {
                $user = User::where('phone', $request->phone)->first();
                if ($user) {
                    return $this->prepare_response(__('auth.password'), __('auth.failed'), null, 401);
                } else {
                    return $this->prepare_response(__('auth.wrong phone'), __('auth.failed'), null, 401);
                }
            } else {
                $user = Auth::user();
                if ($user->role != 'end-user') {
                    return $this->prepare_response(__('auth.not allowed'),  __('auth.failed'), null, 401);
                }
            }
            if($request['fcm_token'])
            {
                $user->fcm()->firstOrCreate(['fcm_token' => $request['fcm_token']]);
                $user->save();
            }
            return $this->prepare_response(null, __('auth.registered') . __('auth.successfully'), ['user' => Auth::user()->load('address.city'), 'token' => $token], 200);
        } catch (JWTException $e) {
          
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    public function adminLogin(Request $request)
    {
        $credentials = $request->only('phone', 'password', 'fcm_token');
        $validator = Validator::make($credentials, [
            'phone' => 'required|numeric',
            'password' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return $this->prepare_response($validator->errors(), __('validation.failed'), null, 400);
        }
        try {
            $request->phone = intval($request->phone); 
           // return $request;

            if ( !$token = JWTAuth::attempt(['phone' => intval($request->phone), 'password' => $request->password])) {

                $user = User::where('phone', $request->phone)->first();
                
                if ($user) {
                    return $this->prepare_response(__('auth.password'), __('auth.failed'), null, 401);
                } else {
                    return $this->prepare_response(__('auth.wrong phone'), __('auth.failed'), null, 401);
                }
                


            } else {
                $user = Auth::user();

                
                if ($user->role != 'super-admin' && $user->role != 'store-admin') {
                    return $this->prepare_response(__('auth.not allowed'),  __('auth.failed'), null, 401);
                } elseif ($user->role == 'store-admin') {

                    if ($user->store()->count() == 0) {
                        return $this->prepare_response(__('auth.no store'), __('auth.not allowed'), null, 401);
                    } else {
                        if($request['fcm_token'])
                        {
                            $user->fcm()->firstOrCreate(['fcm_token' => $request['fcm_token']]);
                            $user->save();
                        }
                        $user['class_a_access'] = $user->store()->first()->class_a_access;
                        $user['is_featured'] = $user->store()->first()->is_featured;
                        return $this->prepare_response(null, __('auth.registered') . __('auth.successfully'), ['user' => Auth::user()->load(['address.city']), 'token' => $token], 200);
                    }
                }

                if($request['fcm_token'])
                {
                    $user->fcm()->firstOrCreate(['fcm_token' => $request['fcm_token']]);
                    $user->save();
                }
            }

            return $this->prepare_response(null, __('auth.registered') . __('auth.successfully'), ['user' => Auth::user()->load('address.city'), 'token' => $token], 200);
        } catch (JWTException $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        try {

            $password = $request['password'];
            $request['password'] = bcrypt($request->password);
            $user = User::create($request->all());
            if ($request->hasFile('image')) {
                $image_path = $request->file('image')->store('/users/' . $user->id, 's3');
                Storage::disk('s3')->setVisibility($image_path, 'public');
                $user->image = Storage::disk('s3')->url($image_path);
                $user->save();
            }
            if ($request->has('title') && $request->has('longitude') && $request->has('latitude') && $request->has('city_id')) {
                $user->address()->create($request->except('password'));
            }
            $user->fcm()->firstOrCreate(['fcm_token' => $request['fcm_token']]);

            $token = JWTAuth::attempt(['phone' => $request->phone, 'password' =>  $password]);
            return $this->prepare_response(null, __('auth.registered') . __('auth.successfully'), ['user' => Auth::user()->load('address.city'), 'token' => $token], 200);
        } catch (JWTException $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $credentials = $request->only('fcm_token');
        $validator = Validator::make($credentials, [
            'fcm_token' => 'required|string',
        ]);
        if ($validator->fails()) {
            return $this->prepare_response($validator->errors(), __('validation.failed'), null, 400);
        }
        try {
            $user = Auth::user();
            $user->fcm()->where('fcm_token', $request->fcm_token)->delete();
            Auth::logout();
            JWTAuth::invalidate(JWTAuth::getToken());
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), null, 200);
        } catch (JWTException $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        try {
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), Auth::user()->load('address.city'), 200);
        } catch (JWTException $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * Aupdate user data.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        try {
            $request['password'] = bcrypt($request->password);
            $user = Auth::user();
            if ($request->hasFile('image')) {
                $delete_old_image = Storage::disk('s3')->delete($user->image);
                if ($delete_old_image) {
                    $image_path = $request->file('image')->store('/users/' . $user->id , 's3');
                    Storage::disk('s3')->setVisibility($image_path, 'public');
                    $user->image = Storage::disk('s3')->url($image_path);
                    $user->save();
                }
            }
            $user->update($request->except('image'));
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'),
            User::where('id',$user->id)->with('address')->get(), 200);
        } catch (JWTException $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function followedStores()
    {
        try {
            $stores = Auth::user()->load('followedStores:id,name,logo');
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $stores->followedStores, 200);
        } catch (JWTException $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * unfollow a store.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function unfollowStore($store_id)
    {
        try {
            Auth::user()->followedStores()->wherePivot('store_id', '=', $store_id)->detach();
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), Auth::user()->load('followedStores'), 200);
        } catch (JWTException $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }


    /**
     * Add user address.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addAddress(AddAddressRequest $request)
    {
        try {
            $user = Auth::user();
            $request['user_id'] = $user->id;
            $address = Address::create($request->all());
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $address->load('city'), 200);
        } catch (JWTException $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
     * Aupdate user address.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAddress(UpdateAddressRequest $request)
    {
        try {
            $user = Auth::user();
            $address = $user->address()->where('id', $request['address_id']);
            $address->update($request->except('address_id'));

            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), Address::where('id', $request['address_id'])->with('city')->first(), 200);
        } catch (JWTException $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
     * Aupdate user address.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAddress(DeleteAddressRequest $request)
    {
        try {
            $user = Auth::user();
            $address = $user->address()->where('id', $request['address_id']);
            $address->delete();
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), Auth::user()->load('address.city'), 200);
        } catch (JWTException $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * Check Phone.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function checkPhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|numeric|min:10|exists:users,phone',
        ]);
        if ($validator->fails()) {
            return $this->prepare_response($validator->errors(), __('validation.failed'), null, 400);
        }
        try {
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), true, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
     * reset Password.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|numeric|min:10|exists:users,phone',
            'password' => 'required|min:6|string|confirmed',

        ]);
        if ($validator->fails()) {
            return $this->prepare_response($validator->errors(), __('validation.failed'), null, 400);
        }
        try {
            $user = User::where('phone', $request->phone)->first();
            $user->password = bcrypt($request->password);
            $user->save();
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'),  $user, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
}
