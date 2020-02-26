<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class BuyerController extends ApiController
{
     public function __construct()
    {
       parent::__construct();//llama al constructor de la clase padre
       $this->middleware('scope:read-general')->only(['show']);//permite o restringe lectura
       //permisos mediante policy
       $this->middleware('can:view,buyer')->only('show');
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

        //obtener todos los compradores
        $compradores = Buyer::has('transactions')->get();

        return $this->showAll($compradores);//response()->json(['data'=> $compradores], 200);
    }

    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Buyer $buyer)//inyeccion de dependencia, tiene un metodo boot en el modelo para filtrar
    {
        //buscar el comprador
         return $this->showOne($buyer);
    }
    
}
