<?php

namespace App\Transformers;

use App\Category;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Category $category)
    {
        return [
            //regresa los datos transformados al valor dado
            'identificador' => (int)$category->id,
            'titulo' =>(string)$category->name,
            'detalles' =>(string)$category->description,
            'fechaCreacion' => (string)$category->created_at,
            'fechaActualizacion' => (string)$category->updated_at,
            'fechaEliminacion' => isset($category->deleted_at) ? (string)$category->deleted_at : null,
            //uso de HATEOAS(links con informacion relacionada)
            'links' => [
                //enlace a si mismo
                [
                    'rel' => 'self',
                    'href' => route('categories.show', $category->id),
                ],
                //enlace a los compradores de la categoria
                [
                    'rel' => 'category.buyers',
                    'href' => route('categories.buyers.index', $category->id),
                ],
                //enlace a los productos de la categoria
                [
                    'rel' => 'category.products',
                    'href' => route('categories.products.index', $category->id),
                ], 
                 //enlace a los vendedores de la categoria
                [
                    'rel' => 'category.sellers',
                    'href' => route('categories.sellers.index', $category->id),
                ],  //enlace a los transacciones de la categoria
                [
                    'rel' => 'category.transactions',
                    'href' => route('categories.transactions.index', $category->id),
                ]
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
            'created_at' => 'fechaCreacion',
            'updated_at' => 'fechaActualizacion',
            'deleted_at' => 'fechaEliminacion',
        ];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    } 
}
