<?php

namespace App\Http\Middleware;

use Closure;

//agregar una cabecera a la respuesta http
class SignatureMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */                                                                 //Pre-Fijo X para crear
    public function handle($request, Closure $next, $header = 'X-Name') //cabecera personalizada 
    {
        $response = $next($request);
        //agregar la cabecera a la respuesta
        $response->headers->set($header, config('app.name') );
        
        return $response;
    }
}
