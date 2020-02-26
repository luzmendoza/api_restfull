<?php

namespace App\Http\Controllers\Transaction;

use App\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class TransactionSellerController extends ApiController
{
     public function __construct()
    {
       parent::__construct();//llama al constructor de la clase padre
       $this->middleware('scope:read-general')->only(['index']);//permite o restringe lectura
       $this->middleware('can:view,transaction')->only('index');//policy
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   public function index(Transaction $transaction)
    {
        $seller = $transaction->product->seller;
        return $this->showOne($seller);
    }

}
