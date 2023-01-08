<?php

namespace App\Services;

use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use Auth;
use Illuminate\Http\Request;

class TransactionService
{
    protected $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = new TransactionRepository($transaction);
    }
    /**
       * Display a listing of the resource.
       */
    public function index()
    {
        return $this->transaction->index();
    }
    /**
       * Display a listing of the resource.
       */
    public function invoices()
    {
        return $this->transaction->invoices();
    }
    
   
    /**
     * Store a newly created resource in storage.
     */
    public function store($request)
    {
        return $this->transaction->store($request);
    }

    
    /**
     * Update the specified resource in storage.
     */
    public function update($request, $id)
    {
        return $this->transaction->update($request, $id);
    }

   

    /**
     * Return back the count for all the transactions with each statue.
     */
    public function statistics()
    {
        return $this->transaction->statistics();
    }

    public function report($request)
    {
        return $this->transaction->report($request);

    }
}
