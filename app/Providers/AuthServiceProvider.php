<?php

namespace App\Providers;

use App\User;
use App\Buyer;
use App\Seller;
use App\Product;
use Carbon\Carbon;
use App\Transaction;
use App\Policies\UserPolicy;
use App\Policies\BuyerPolicy;
use App\Policies\SellerPolicy;
use Laravel\Passport\Passport;
use App\Policies\ProductPolicy;
use App\Policies\TransactionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        Buyer::class => BuyerPolicy::class,
        Seller::class => SellerPolicy::class,
        User::class => UserPolicy::class,
        Transaction::class => TransactionPolicy::class,
        Product::class => ProductPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        //registra las politicas de acceso a rutas
        $this->registerPolicies();

        //definir un gate para las acciones del administrador
        Gate::define('admin-action', function ($user) {
            return $user->esAdministrador();
        });

        //obtener laravel passport para autenticacion mediante clientes oauth
        Passport::routes();
        //crear expiracion de tokens
        Passport::tokensExpireIn(Carbon::now()->addMinutes(30));//valido durante 30 segundos o minutos
        //hacer un refresh de tokens cuando ya estan expirados
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));//durante 30 dias
        //cuando un token expira, el cliente tiene maximo 30 dias para refrescar y utilizar uno nuevo

        //para hacer un token sin que se guarde el secreto del cliente
        Passport::enableImplicitGrant();

        //registro de scopes (permisos a rutas/acciones)
        Passport::tokensCan([
            'purchase-product' => 'Crear transacciones para comprar productos determinados',
            'manage-products' => 'Crear, ver, actualizar y eliminar productos',
            'manage-account' => 'Obtener la informacion de la cuenta, nombre, email, estado (sin contraseña), modificar datos como email, nombre y contraseña. No puede eliminar la cuenta',
            'read-general' => 'Obtener información general, categorías donde se compra y se vende, productos vendidos o comprados, transacciones, compras y ventas',
        ]);
    }
}
