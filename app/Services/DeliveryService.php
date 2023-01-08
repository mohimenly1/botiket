<?php

namespace App\Services;

use App\Models\Delivery;
use App\Repositories\DeliveryRepository;
use Auth;
use Illuminate\Http\Request;

class DeliveryService
{
    protected $delivery;

    public function __construct(Delivery $delivery)
    {
        $this->delivery = new DeliveryRepository($delivery);
    }
    /**
       * Display a listing of the resource.
       */
    public function index()
    {
        return $this->delivery->index();
    }
    /**
       * Display a listing of the resource.
       */
    public function indexDeleted()
    {
        return $this->delivery->indexDeleted();
    }
   
    /**
     * Store a newly created resource in storage.
     */
    public function store($request)
    {
        return $this->delivery->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return $this->delivery->showUser($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($request, $id)
    {
        return $this->delivery->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return $this->delivery->destroy($id);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        return $this->delivery->restore($id);
    }
    public function report($request)
    {
        return $this->delivery->report($request);

    }
}
