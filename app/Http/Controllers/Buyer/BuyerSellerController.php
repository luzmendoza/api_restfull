<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class BuyerSellerController extends ApiController
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
    public function index(Buyer $buyer)
    {
        //valida si es administrador
        $this->allowedAdminAction();

        //obtener la lista
        $products = $buyer->transactions()->with('product.seller')//obtiene los vendedores
            ->get()
            ->pluck('product.seller')
            ->unique('id')
            ->values();//organiza y elimina los vacios
        return $this->showAll($products);
    }

    
}
