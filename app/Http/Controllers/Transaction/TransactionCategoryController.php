<?php

namespace App\Http\Controllers\Transaction;

use App\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class TransactionCategoryController extends ApiController
{
    //registro del middleware para accesos de solo visualizacion
    public function __construct()
    {
        $this->middleware('client.credentials')->only(['index']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Transaction $transaction)
    {
        //recuperar las categorias involucradas en la transaccion, a traves del producto de la transaccion
        $categories = $transaction->product->categories;
        return $this->showAll($categories);
    }

}
