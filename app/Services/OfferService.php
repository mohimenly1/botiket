<?php

namespace App\Services;

use App\Models\Offer;
use App\Repositories\OfferRepository;
use Auth;
use Illuminate\Http\Request;

class OfferService
{
    protected $offer;

    public function __construct(Offer $offer)
    {
        $this->offer = new OfferRepository($offer);
    }
    /**
       * Display a listing of the resource.
       */
    public function index()
    {
        return $this->offer->index();
    }
    /**
       * Display a listing of the resource.
       */
    public function indexDeleted()
    {
        return $this->offer->indexDeleted();
    }
   
    /**
     * Offer a newly created resource in storage.
     */
    public function store($request)
    {
        return $this->offer->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return $this->offer->show($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($request, $id)
    {
        return $this->offer->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return $this->offer->destroy($id);
    }

    /**
     * Reoffer the specified resource from storage.
     */
    public function reoffer($id)
    {
        return $this->offer->reoffer($id);
    }
}
