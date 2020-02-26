<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class BuyerCategoryController extends ApiController
{
     public function __construct()
    {
       parent::__construct();//llama al constructor de la clase padre
       $this->middleware('scope:read-general')->only(['index']);//permite o restringe lectura
       //permisos mediante policy
       $this->middleware('can:view,buyer')->only('index');//accion,metodo,recurso
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Buyer $buyer)
    {
        $categories = $buyer->transactions()->with('product.categories')//obtiene los vendedores
            ->get()
            ->pluck('product.categories')
            ->collapse()//hace solo una lista de categorias sin repetidas
            ->unique('id')
            ->values();//organiza y elimina los vacios

        // dd($categories);
        return $this->showAll($categories);
    }
}
