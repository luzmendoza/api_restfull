<?php

namespace App\Http\Controllers\Product;

use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class ProductBuyerController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Product $product)
    {
        //
        $buyers = $product->transactions()
                    ->with('buyer')//lista de compradores
                    ->get()//obtener los datos
                    ->pluck('buyer')//obtener solo las collecciones de los buyers
                    ->unique('id')//usarlo para evitar los buyers duplicados
                    ->values();//usarlos para evitar datos vacios

        return $this->showAll( $buyers);
    }

    
}
