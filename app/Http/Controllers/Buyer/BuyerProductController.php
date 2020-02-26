<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class BuyerProductController extends ApiController
{
     public function __construct()
    {
       parent::__construct();//llama al constructor de la clase padre
       $this->middleware('scope:read-general')->only(['index']);//permite o restringe lectura
       //permisos mediante policy
       $this->middleware('can:view,buyer')->only('index');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Buyer $buyer)
    {
        //incluir la lista de los productos de las transacciones
        $products = $buyer->transactions()->with('product')
            ->get()
            ->pluck('product');
        return $this->showAll($products);
    }
}
