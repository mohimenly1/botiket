<?php

namespace App\Http\Controllers;

use App\Http\Traits\ResponseTraits;
use App\Services\UserService;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ResponseTraits;
    protected $user_service;
    public function __construct(UserService $service)
    {
        $this->user_service = $service;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = $this->user_service->index();
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    public function getUnpaginatedUsers(){
        try {
            $data = User::where('role','end-user')->with('address.city')->get(['id','name']);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
  
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
        * Display a listing of the resource.
        *
        * @return \Illuminate\Http\Response
        */
    public function indexDeleted()
    {
        try {
            $data = $this->user_service->indexDeleted();
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
        * Display a listing of the resource.
        *
        * @return \Illuminate\Http\Response
        */
    public function indexStoreAdmin()
    {
        try {
            $data = $this->user_service->indexStoreAdmin();
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
        * Display a listing of the resource.
        *
        * @return \Illuminate\Http\Response
        */
    public function indexDeletedStoreAdmin()
    {
        try {
            $data = $this->user_service->indexDeletedStoreAdmin();
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        try {
        $data = $this->user_service->store($request);
        return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response( [$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $data = $this->user_service->show($id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {
        try {
            $data = $this->user_service->update($request, $id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $data = $this->user_service->destroy($id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        try {
            $data = $this->user_service->restore($id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    public function report(Request $request)
    {
        try {
            $data = $this->user_service->report($request);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    
}
