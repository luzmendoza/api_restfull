<?php

namespace App\Http\Controllers\Product;

use App\Product;
use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class ProductCategoryController extends ApiController
{
    //registro del middleware para accesos de solo visualizacion
    public function __construct()
    {
        $this->middleware('client.credentials')->only(['index']);//informacion publica
        $this->middleware('auth:api')->except(['index']);
        $this->middleware('scope:manage-products')->except('index');//permite o restringe crear, actualizar y eliminar categoria de productos
        //restricciones con policies
        $this->middleware('can:add-category,product')->only('update');//separar con guion nombre
        $this->middleware('can:delete-category,product')->only('destroy');//separar con guion
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Product $product)
    {
        //
        $categories = $product->categories;

        return $this->showAll($categories);
    }

    //actualizar la relacion que existe de producto-categoria
    public function update(Request $request, Product $product, Category $category)
    {
        //metodo que agrega sin duplicar la categoria
        $product->categories()->syncwithoutDetaching([$category->id]);

        return $this->showAll($product->categories);
    }

    //eliminar la relacion de la categoria-producto
    public function destroy(Product $product, Category $category)
    {
        //verificar que exista la relacion
        if (!$product->categories()->find($category->id)) {
            # code...
            return $this->errorResponse('La categoria especificada no es una categoria de este producto', 404);
        }

        //eliminar la relacion
         $product->categories()->detach([$category->id]);

         //regresar
         return $this->showAll($product->categories);


    }
  
}
