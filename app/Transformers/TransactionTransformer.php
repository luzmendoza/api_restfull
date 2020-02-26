<?php

namespace App\Transformers;

use App\Transaction;
use League\Fractal\TransformerAbstract;

class TransactionTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Transaction $transaction)
    {
         return [
            //regresa los datos transformados al valor dado
            'identificador' => (int)$transaction->id,
            'cantidad' =>(int)$transaction->quantity,
            'comprador' =>(int)$transaction->buyer_id,
            'producto' =>(int)$transaction->product_id,
            'fechaCreacion' => (string)$transaction->created_at,
            'fechaActualizacion' => (string)$transaction->updated_at,
            'fechaEliminacion' => isset($transaction->deleted_at) ? (string)$transaction->deleted_at : null,

            //uso de HATEOAS(links con informacion relacionada)
            'links' => [
                //enlace a si mismo
                [
                    'rel' => 'self',
                    'href' => route('transactions.show', $transaction->id),
                ],
                //enlace a los categorias de transactiono
                [
                    'rel' => 'transaction.categories',
                    'href' => route('transactions.categories.index', $transaction->id),
                ], 
                //vendedor
                [
                    'rel' => 'transaction.seller',
                    'href' => route('transactions.sellers.index', $transaction->id),
                ], 
                //relacion transaction comprador
                [
                    'rel' => 'buyer',
                    'href' => route('buyers.show', $transaction->buyer_id),
                ], 
                 //relacion transaction producto
                [
                    'rel' => 'product',
                    'href' => route('products.show', $transaction->product_id),
                ], 
            ],
        ];
    }

    public static function originalAttribute($index)
    {
        $attributes = [
            //regresa los datos transformados al valor dado
            'identificador' => 'id',
            'cantidad' =>'quantity',
            'comprador' =>'buyer_id',
            'producto' =>'product_id',
            'fechaCreacion' => 'created_at',
            'fechaActualizacion' => 'updated_at',
            'fechaEliminacion' => 'deleted_at'
        ];

        //si el filtrado es por un campo que este en nuestros atributos
        return isset($attributes[$index]) ? $attributes[$index] : null;
    } 

    public static function transformedAttribute($index)
    {
        $attributes = [
            'id' => 'identificador',
            'quantity' => 'cantidad',
            'buyer_id' => 'comprador',
            'product_id' => 'producto',
            'created_at' => 'fechaCreacion',
            'updated_at' => 'fechaActualizacion',
            'deleted_at' => 'fechaEliminacion',
        ];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}
