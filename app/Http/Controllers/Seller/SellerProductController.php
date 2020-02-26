<?php

namespace App\Http\Controllers\Seller;

use App\User;
use App\Seller;
use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Storage;
use App\Transformers\ProductTransformer;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SellerProductController extends ApiController
{
    //registro del middleware
    public function __construct()
    {
        parent::__construct();
        $this->middleware('transform.input:' . ProductTransformer::class)->only(['store', 'update']);
        $this->middleware('scope:manage-products')->except('index');//permite o restringe crear, actualizar y eliminar productos
        $this->middleware('can:view,seller')->only('index');//policy
        $this->middleware('can:sale,seller')->only('store');//policy
        $this->middleware('can:edit-product,seller')->only('update');//policy... se pone con guion editProduct
        $this->middleware('can:delete-product,seller')->only('destroy');//policy
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Seller $seller)
    {
        //validar el permiso para la accion mediante tokens
       if (request()->user()->tokenCan('read-general') || request()->user()->tokenCan('manage-products')) {
            //regresa una lista de productos del vendedor seleccioando
            $products = $seller->products;
            return $this->showAll($products);
        }
        //respuesta en caso de no estar autenticado
        throw new AuthenticationException;
    }

   

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $seller)
    {
        //reglas de validacion
        $rules = [
            'name' => 'required',
            'description' => 'required',
            'quantity' => 'required|integer|min:1',
            'image' => 'required|image',
        ];
        //ejecutar la validacion
        $this->validate($request, $rules);
        //obtener los datos
        $data = $request->all();

        //modificaciones en los datos
        $data['status'] = Product::PRODUCTO_NO_DISPONIBLE;
        $data['image'] = $request->image->store('');
        $data['seller_id'] = $seller->id;

        //crear el producto
        $product = Product::create($data);

        return $this->showOne($product, 201);
    }

   
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Seller $seller, Product $product)
    {
        //reglas de validacion
        $rules = [
            'quantity' => 'integer|min:1',
            'status' => 'in: '. Product::PRODUCTO_DISPONIBLE . ',' . Product::PRODUCTO_NO_DISPONIBLE,
            'image' => 'image',
        ];
        //ejecutar la validacion
        $this->validate($request, $rules);

        //propias verificaciones
        $this->verificarVendedor($seller, $product);

        //llenar instancia a actualizar
        $product->fill($request->only([
                'name',
                'description',
                'quantity',
        ]));

        //cambiar el estado si se tienen por lo menos una categoria
        if ($request->has('status')) {
            # code...
            $product->status = $request->status;

            if ($product->estaDisponible() && $product->categories()->count() === 0) {
                # code...
                return $this->errorResponse('Un producto activo debe tener por lo menos una categoria', 409);
            }
        }

        //actualizar imagen
        if ($request->hasFile('image')) {
            # eliminar y luego volver a crear
            Storage::delete($product->image);
            //crear
            $product->image = $request->image->store('');//se manda en vacio porque ya quedo definida la carpeta donde se guardaran en las configuracion de archivos
        }

        //validar si se realizo alguna modificacion 
        if ($product->isClean()) {
            # code...
              return $this->errorResponse('Se debe especificar por lo menos un valor diferente para actualizar', 422);
        }

        $product->save();

        return $this->showOne($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function destroy(Seller $seller, Product $product)
    {
        //
        $this->verificarVendedor($seller, $product);

        //ELIMINAR LA IMAGEN DEL PRODUCTO
        Storage::delete($product->image);

        $product->delete();

        return $this->showOne($product);
    }

    public function verificarVendedor(Seller $seller, Product $product)
    {
        if ($seller->id != $product->seller_id) {
           
            throw new HttpException(422,'El vendedor no es el dueño del producto');
        }
    }
}
