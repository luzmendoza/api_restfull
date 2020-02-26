<?php

namespace App\Http\Controllers\Seller;

use App\Seller;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class SellerController extends ApiController
{
     public function __construct()
    {
       parent::__construct();//llama al constructor de la clase padre
       $this->middleware('scope:read-general')->only(['show']);//permite o restringe lectura
       $this->middleware('can:view,seller')->only('show');
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
        
        //obtener todos los vendedores
        $sellers = Seller::has('products')->get();

        return $this->showAll($sellers);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Seller $seller)
    {
         //buscar el comprador
        return $this->showOne($seller);
    }

}
