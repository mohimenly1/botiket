<?php

namespace App\Services;

use App\Models\Banner;
use App\Repositories\BannerRepository;
use Auth;
use Illuminate\Http\Request;

class BannerService
{
    protected $banner;

    public function __construct(Banner $banner)
    {
        $this->banner = new BannerRepository($banner);
    }
    /**
       * Display a listing of the resource.
       */
    public function index($request)
    {
        return $this->banner->index($request);
    }
    /**
       * Display a listing of the resource.
       */
    public function indexDeleted()
    {
        return $this->banner->indexDeleted();
    }
   
    /**
     * Store a newly created resource in storage.
     */
    public function store($request)
    {
        return $this->banner->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return $this->banner->showUser($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($request, $id)
    {
        return $this->banner->update($request, $id);
    }

   


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return $this->banner->destroy($id);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        return $this->banner->restore($id);
    }
    public function report($request)
    {
        return $this->banner->report($request);

    }
}
