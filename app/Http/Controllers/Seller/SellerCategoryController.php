<?php

namespace App\Http\Controllers\Seller;

use App\Seller;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class SellerCategoryController extends ApiController
{
     public function __construct()
    {
       parent::__construct();//llama al constructor de la clase padre
       $this->middleware('scope:read-general')->only(['index']);//permite o restringe lectura
       $this->middleware('can:view,seller')->only('index');//con policy
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Seller $seller)
    {
        //
        $categories = $seller->products() //lista de productos del vendedor
                        ->with('categories') //higerloading?
                        ->get() //obtener los resultado
                        ->pluck('categories') //sacar de la coleccion solo transacciones
                        ->collapse()//hacer una sola coleccion
                        ->unique('id') //que sea la unica categoria para no tener duplicados
                        ->values();//elimina elementos vacios

        //regresar el resultado
        return $this->showAll($categories);//este metodo esta en el apicontroller
    }

    
}
