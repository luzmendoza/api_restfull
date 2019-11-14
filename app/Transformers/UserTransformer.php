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
        ];
    }
}
