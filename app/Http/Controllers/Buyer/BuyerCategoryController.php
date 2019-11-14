<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class BuyerCategoryController extends ApiController
{
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
