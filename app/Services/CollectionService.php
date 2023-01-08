<?php

namespace App\Services;

use App\Models\Collection;
use App\Repositories\CollectionRepository;
use Auth;
use Illuminate\Http\Request;

class CollectionService
{
    protected $collection;

    public function __construct(Collection $collection)
    {
        $this->collection = new CollectionRepository($collection);
    }
    /**
       * Display a listing of the resource.
       */
    public function index()
    {
        return $this->collection->index();
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return $this->collection->show($id);
    }
    /**
       * Update the specified resource in storage.
       */
    public function update($request, $id)
    {
        return $this->collection->update($request, $id);
    }
    /**
       * Display a listing of the resource.
       */
    public function indexDeleted()
    {
        return $this->collection->indexDeleted();
    }
   
    /**
     * Store a newly created resource in storage.
     */
    public function store($request)
    {
        return $this->collection->store($request);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return $this->collection->destroy($id);
    }
}
