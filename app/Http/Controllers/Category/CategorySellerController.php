<?php

namespace App\Http\Controllers\Category;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class CategorySellerController extends ApiController
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

        //lista de sellers de cada producto
        $sellers = $category->products()
                    ->with('seller')
                    ->get()
                    ->pluck('seller')
                    ->unique()//sin repetidos
                    ->values();//reconstruye los indices al quitar los repetidos

        return $this->showAll($sellers);
    }

}
