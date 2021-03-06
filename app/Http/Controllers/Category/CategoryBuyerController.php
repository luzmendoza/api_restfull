<?php

namespace App\Http\Controllers\Category;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class CategoryBuyerController extends ApiController
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
    public function index(Category $category)
    {
        //valida si es administrador
        $this->allowedAdminAction();

        //obtiene la lista
        $buyers = $category->products()
                    ->whereHas('transactions')//obtiene los productos que tenga por lo menos una venta
                    ->with('transactions.buyer')//lista de transacciones y el comprador
                    ->get()//obtener los datos
                    ->pluck('transactions')//obtener solo las collecciones de las transacciones
                    ->collapse()//hacer una sola coleccion
                    ->pluck('buyer')//obtener los buyers de la coleccion creada
                    ->unique()//usarlo para evitar los buyers duplicados
                    ->values();//usarlos para evitar datos vacios

        return $this->showAll( $buyers);
    }

   
}
