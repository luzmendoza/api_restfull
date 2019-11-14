<?php

namespace App;

use App\Transformers\UserTransformer;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{

    use Notifiable,SoftDeletes;

    //constantes
    const USUARIO_VERIFICADO = '1';
    const USUARIO_NO_VERIFICADO = '0';

    const USUARIO_ADMINISTRADOR = 'true';
    const USUARIO_REGULAR = 'false';

    //agregar el transformador de datos de salida
    public $transformer = UserTransformer::class;

    protected $table = 'users';
    protected $dates = ['deleted_at']; //esto es para el softdeletes

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email', 
        'password',
        'verified',
        'verification_token',
        'admin',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
      //  'verification_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //mutador
    public function setNameAttribute($valor)
    {   
        //convertir en minusculas el nombre
        $this->attributes['name'] = strtolower($valor);
    }

    //accesor
    public function getNameAttribute($valor)
    {
        //se transforma el valor solo al mostrarlo al usuario
        return ucwords($valor);
    }

    //mutador
    public function setEmaillAttribute($valor)
    {   
        //convertir en minusculas 
        $this->attributes['email'] = strtolower($valor);
    }

    //metodo para saber si un usuario es verificado
    public function esVerificado()
    {
        return $this->verified == User::USUARIO_VERIFICADO;
    }

    //funcion para saber si es administrador
    public function esAdministrador()
    {
        return $this->admin == User::USUARIO_ADMINISTRADOR;
    }

    //funcion para saber si tiene un token valido
    public static function generarVerificationToken()
    {
        return str_random(40);
    }
}
