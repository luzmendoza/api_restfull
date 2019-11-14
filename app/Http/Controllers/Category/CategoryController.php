<?php

namespace App\Http\Controllers\Category;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class CategoryController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //recuperar todos
        $categories = Category::all();
        return $this->showAll($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //reglas
        $rules = [
            'name' => 'required',
            'description' => 'required',
        ];
        //ejecutar validacion
        $this->validate($request,$rules);
        //crear la instancia
        $category = Category::create($request->all());
        //enviar respuesta
        return $this->showOne($category, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
        return $this->showOne($category);
    }

    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        //
        $category->fill($request->only([
            'name',
            'description',
        ]));

        //validar si la instancia recuperada cambio
        if ($category->isClean()) {
            #regresar un valor
            return $this->errorResponse('Debe especificar al  menos un valor diferente para actualizar', 422);

        }
        //guardar si es posible
        $category->save();
        //regresar respuesta
        return $this->showOne($category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        //eliminar
        $category->delete();
        //respuesta
        return $this->showOne($category);
    }
}
