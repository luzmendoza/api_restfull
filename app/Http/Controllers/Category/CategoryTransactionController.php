<?php

namespace App\Http\Controllers\Category;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class CategoryTransactionController extends ApiController
{
    /**LISTA DE TRANSACCIONES PARA UNA CATEGORIA
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Category $category)
    {
        //lista de transacciones por producto de esa categoria
        $transactions = $category->products()
                        ->whereHas('transactions')//obtiene los productos que tenga por lo menos una venta
                        ->with('transactions')//puede que no tenga transacciones y sea lista vacia, por eso se uso el whereHas antes
                        ->get()
                        ->pluck('transactions') //solo la lista de transacciones
                        ->collapse();//se hace solo una lista

        return $this->showAll($transactions);
    }

}
