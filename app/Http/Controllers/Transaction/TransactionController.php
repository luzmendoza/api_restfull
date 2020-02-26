<?php

namespace App\Http\Controllers\Transaction;

use App\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class TransactionController extends ApiController
{
     public function __construct()
    {
       parent::__construct();//llama al constructor de la clase padre
       $this->middleware('scope:read-general')->only(['show']);//permite o restringe lectura
       $this->middleware('can:view,transaction')->only('show');//mediante policy
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //valida si es administrador
        $this->allowedAdminAction();
        
        //regresar todos
        $transactions = Transaction::all();
        return $this->showAll($transactions);
    }

   
    /**
     * Display the specified resource.
     *
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        //recuperar una instancia
        return $this->showOne($transaction);
    }

   
}
