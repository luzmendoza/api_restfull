<?php

namespace App\Http\Controllers\Product;

use App\User;
use App\Product;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiController;
use App\Transformers\TransactionTransformer;

class ProductBuyerTransactionController extends ApiController
{
    //registro del middleware
    public function __construct()
    {
        parent::__construct();
        $this->middleware('transform.input:' . TransactionTransformer::class)->only(['store']);
        $this->middleware('scope:purchase-product')->only('store');//permite o restringe 
        //restringe o permite por policy
        $this->middleware('can:purchase,buyer')->only('store');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Product $product, User $buyer)
    {
        //reglas principales
        $rules = [
            'quantity' => 'required|integer|min:1',
        ];
        //ejecutar esta validacion
        $this->validate($request, $rules);

        //validar si el comprador y el vendedor son iguales
        if ($buyer->id == $product->seller_id) {
            return $this->errorResponse('El comprador y el vendedor deben ser diferentes', 409);
        }

        //que el comprador y el vendedor sean usuarios verificados
        if (!$buyer->esVerificado()) {
            return $this->errorResponse('El comprador debe ser un usuario verificado', 409);
        }

        if (!$product->seller->esVerificado()) {
            return $this->errorResponse('El vendedor deben ser un usuario verificado', 409);
        }

        //validar que el prodcto este disponible
        if (!$product->estaDisponible()) {
            return $this->errorResponse('El producto no esta disponible', 409);
        }

        //validar cantidad de producto
        if ($product->quantity < $request->quantity) {
            return $this->errorResponse('El producto no tiene la cantidad disponible requerida para la transacción', 409);
        }

        //crear la transaccion mediante transacciones de base de datos
        return DB::transaction(function () use($request, $product, $buyer){
            //disminuir la cantidad del producto
            $product->quantity -= $request->quantity;
            $product->save();

            //crear la instancia
            $transaction = Transaction::create([
                'quantity' => $request->quantity,
                'buyer_id' => $buyer->id,
                'product_id' => $product->id,
            ]);

            //regresar la respuesta
            return $this->showOne($transaction, 201);

        });
    }

}
