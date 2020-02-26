<?php

namespace App\Transformers;

use App\Product;
use League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Product $product)
    {
         return [
            //regresa los datos transformados al valor dado
            'identificador' => (int)$product->id,
            'titulo' =>(string)$product->name,
            'detalles' =>(string)$product->description,
            'disponibles' =>(int)$product->quantity,
            'estado' =>(string)$product->status,
            'imagen' => url("img/{$product->image}"),
            'vendedor' =>(int)$product->seller_id,
            'fechaCreacion' => (string)$product->created_at,
            'fechaActualizacion' => (string)$product->updated_at,
            'fechaEliminacion' => isset($product->deleted_at) ? (string)$product->deleted_at : null,

             //uso de HATEOAS(links con informacion relacionada)
            'links' => [
                //enlace a si mismo
                [
                    'rel' => 'self',
                    'href' => route('products.show', $product->id),
                ],
                //enlace a los compradores de producto
                [
                    'rel' => 'product.buyers',
                    'href' => route('products.buyers.index', $product->id),
                ],
                //enlace a los categorias de producto
                [
                    'rel' => 'product.categories',
                    'href' => route('products.categories.index', $product->id),
                ], 
                [
                    'rel' => 'product.transactions',
                    'href' => route('products.transactions.index', $product->id),
                ], 
                //relacion producto vendedor
                [
                    'rel' => 'seller',
                    'href' => route('sellers.show', $product->seller_id),
                ], 
            ],
        ];
    }

    public static function originalAttribute($index)
    {
        $attributes = [
            //regresa los datos transformados al valor dado
            'identificador' => 'id',
            'titulo' =>'name',
            'detalles' =>'description',
            'disponibles' =>'quantity',
            'estado' =>'status',
            'imagen' => 'image',
            'vendedor' =>'seller_id',
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
            'name' => 'titulo',
            'description' => 'detalles',
            'quantity' => 'disponibles',
            'status' => 'estado',
            'image' => 'imagen',
            'seller_id' => 'vendedor',
            'created_at' => 'fechaCreacion',
            'updated_at' => 'fechaActualizacion',
            'deleted_at' => 'fechaEliminacion',
        ];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}
