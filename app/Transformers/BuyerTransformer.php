<?php

namespace App\Transformers;

use App\Buyer;
use League\Fractal\TransformerAbstract;

class BuyerTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Buyer $buyer)
    {
        return [
            //regresa los datos transformados al valor dado
            'identificador' => (int)$buyer->id,
            'nombre' =>(string)$buyer->name,
            'correo' => (string)$buyer->email,
            'esVerificado' => (int)$buyer->verified,
            'fechaCreacion' => (string)$buyer->created_at,
            'fechaActualizacion' => (string)$buyer->updated_at,
            'fechaEliminacion' => isset($buyer->deleted_at) ? (string)$buyer->deleted_at : null,

            //uso de HATEOAS(links con informacion relacionada)
            'links' => [
                //enlace a si mismo
                [
                    'rel' => 'self',
                    'href' => route('buyers.show', $buyer->id),
                ],
                //enlace a los compradores de la categoria
                [
                    'rel' => 'buyer.categories',
                    'href' => route('buyers.categories.index', $buyer->id),
                ],
                //enlace a los productos de la categoria
                [
                    'rel' => 'buyer.products',
                    'href' => route('buyers.products.index', $buyer->id),
                ], 
                 //enlace a los vendedores de la categoria
                [
                    'rel' => 'buyer.sellers',
                    'href' => route('buyers.sellers.index', $buyer->id),
                ],  //enlace a los transacciones de la categoria
                [
                    'rel' => 'buyer.transactions',
                    'href' => route('buyers.transactions.index', $buyer->id),
                ],
                [
                    'rel' => 'user',
                    'href' => route('users.show', $buyer->id),
                ],
            ],
        ];
    }

    public static function originalAttribute($index)
    {
        $attributes = [
            //regresa los datos transformados al valor dado
            'identificador' => 'id',
            'nombre' =>'name',
            'correo' => 'email',
            'esVerificado' => 'verified',
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
            'name' => 'nombre',
            'email' => 'correo',
            'verified' => 'esVerificado',
            'created_at' => 'fechaCreacion',
            'updated_at' => 'fechaActualizacion',
            'deleted_at' => 'fechaEliminacion',
        ];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}
