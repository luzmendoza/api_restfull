<?php

namespace App\Http\Controllers\Seller;

use App\Seller;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class SellerTransactionController extends ApiController
{
     public function __construct()
    {
       parent::__construct();//llama al constructor de la clase padre
       $this->middleware('scope:read-general')->only(['index']);//permite o restringe lectura
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Seller $seller)
    {
        //obtener lista de transacciones de ese vendedor
        $transactions = $seller->products() //lista de productos del vendedor
                        ->whereHas('transactions') //solo los que tengan transacciones
                        ->with('transactions') //higerloading?
                        ->get() //obtener los resultado
                        ->pluck('transactions') //sacar de la coleccion solo transacciones
                        ->collapse(); //junta todo en una lista

        //regresar el resultado
        return $this->showAll($transactions);//este metodo esta en el apicontroller
    }

    
}
