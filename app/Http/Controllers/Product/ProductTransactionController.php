<?php

namespace App\Http\Controllers\Product;

use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class ProductTransactionController extends ApiController
{
     public function __construct()
    {
       parent::__construct();//llama al constructor de la clase padre
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Product $product)
    {
        //valida si es administrador
        $this->allowedAdminAction();

        //obtiene las transacciones de los productos
        $transactions = $product->transactions;

        return $this->showAll($transactions);
    }

   
}
