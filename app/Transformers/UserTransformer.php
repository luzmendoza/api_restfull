<?php

namespace App\Transformers;

use App\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            //regresa los datos transformados al valor dado
            'identificador' => (int)$user->id,
            'nombre' =>(string)$user->name,
            'correo' => (string)$user->email,
            'esVerificado' => (int)$user->verified,
            'esAdministrador' => ($user->admin === 'true'), //regresa el valor de verdad
            'fechaCreacion' => (string)$user->created_at,
            'fechaActualizacion' => (string)$user->updated_at,
            'fechaEliminacion' => isset($user->deleted_at) ? (string)$user->deleted_at : null,

            //uso de HATEOAS(links con informacion relacionada)
            'links' => [
                //enlace a si mismo
                [
                    'rel' => 'self',
                    'href' => route('users.show', $user->id),
                ], 
            ],
        ];
    }

    //transforma los datos de como los ve el usuario a como se deben guardar en bd
    public static function originalAttribute($index)
    {
        $attributes = [
            //regresa los datos transformados al valor dado
            'identificador' => 'id',
            'nombre' => 'name',
            'correo' => 'email',
            'esVerificado' => 'verified',
            'esAdministrador' => 'admin', //regresa el valor de verdad
            'fechaCreacion' => 'created_at',
            'fechaActualizacion' => 'updated_at',
            'fechaEliminacion' => 'deleted_at'
        ];

        //si el filtrado es por un campo que este en nuestros atributos
        return isset($attributes[$index]) ? $attributes[$index] : null;
    } 

    //transforma los datos originales a uno apto para mostrar al usuario
    public static function transformedAttribute($index)
    {
        $attributes = [
            //regresa los datos transformados al valor dado
            'id' => 'identificador',
            'name' => 'nombre',
            'email' => 'correo',
            'verified' => 'esVerificado',
            'admin' => 'esAdministrador', //regresa el valor de verdad
            'created_at' => 'fechaCreacion',
            'updated_at' => 'fechaActualizacion',
            'deleted_at' => 'fechaEliminacion'
        ];

        //si el filtrado es por un campo que este en nuestros atributos
        return isset($attributes[$index]) ? $attributes[$index] : null;
    } 
}
