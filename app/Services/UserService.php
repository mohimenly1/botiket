<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Auth;
use Illuminate\Http\Request;

class UserService
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = new UserRepository($user);
    }
    /**
       * Display a listing of the resource.
       */
    public function index()
    {
        return $this->user->getWhereRole('end-user', false,'address.city');
    }
    /**
       * Display a listing of the resource.
       */
    public function indexDeleted()
    {
        return $this->user->getWhereRole('end-user', true,'address.city');
    }
    /**
        * Display a listing of the resource.
        */
    public function indexStoreAdmin()
    {
        return $this->user->getWhereRole('store-admin',false,'store');
    }
    /**
        * Display a listing of the resource.
        */
    public function indexDeletedStoreAdmin()
    {
        return $this->user->getWhereRole('store-admin', true,'store');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($request)
    {
        return $this->user->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return $this->user->showUserWithRelation($id,'address.city');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($request, $id)
    {
        return $this->user->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return $this->user->destroy($id);
    }

      /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        return $this->user->restore($id);
    }
    public function report($request)
    {
        return $this->user->report($request);

    }
}
