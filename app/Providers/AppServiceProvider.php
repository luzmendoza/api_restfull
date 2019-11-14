<?php

namespace App\Providers;

use App\User;
use App\Product;
use App\Mail\UserCreated;
use App\Mail\UserMailChanged;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //para evistar un error de la version de la base de datos por el nuevo utf8mb4
        Schema::defaultStringLength(191);

        //evento para enviar correos cuando se crea un nuevo usuario
        User::created(function($user){
            //funcion retry... sirve para intentar ejecutar una accion varias veces cuando hay un error
            retry(5, function() use ($user) //numero de intentos a realizar
            {  //funcion a ejecutar
                Mail::to($user)->send(new UserCreated($user));
            },200); //tiempo de espera entre intentos    
        });

        //evento para enviar correos cuando se actualiza solo el email del usuario
        User::updated(function($user){
            if ($user->isDirty('email')) {
                 retry(5,function() use ($user) 
                    {
                         Mail::to($user)->send(new UserMailChanged($user));
                     },200);
            }
        });
        //evento que se ejecutara cuando un producto sea actualizado
        Product::updated(function($product){
            if ($product->quantity == 0 && $product->estaDisponible()) {
                $product->status = Product::PRODUCTO_NO_DISPONIBLE;

                $product->save();
            }
        });
    }
}
