<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\AuthorizationException;

class ApiController extends Controller
{
    //
    use ApiResponser;

    public function __construct()
    {
    	//protegiendo rutas independientes del grant_type
    	$this->middleware('auth:api');//se le envia el guard api para usar este y no passport
    }

    //valida si el usuario es administrador
    protected function allowedAdminAction()
    {
	    if (Gate::denies('admin-action')) {
            throw new AuthorizationException('Esta acción no te es permitida');
        }    	
    }
}
