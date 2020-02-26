<?php

namespace App\Http\Controllers\User;

use App\User;
use App\Mail\UserCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Transformers\UserTransformer;
use App\Http\Controllers\ApiController;

class UserController extends ApiController
{
    //utilizando un middleware para transformar los datos de entrada y usar los transformadores
    public function __construct()
    {
        //permitir crear nuevos usuario y reenviar el correo
        $this->middleware('client.credentials')->only(['store', 'resend']);
        $this->middleware('auth:api')->except(['store', 'verify', 'resend']);
        //registrar el middleware
        $this->middleware('transform.input:'. UserTransformer::class)->only(['store', 'update']);
        $this->middleware('scope:manage-account')->only(['show','update']);//permite o restringe  actualizar y visualizar
        //restricciones mediante policies
        $this->middleware('can:view,user')->only('show');
        $this->middleware('can:update,user')->only('update');
        $this->middleware('can:delete,user')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //valida si es administrador
        $this->allowedAdminAction();

        //acceder a la lista completa de usuarios
        $usuarios = User::all();
        //regresar la lista
       return $this->showAll($usuarios);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //reglas de validacion
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ];

        //validar las reglas
        $this->validate($request,$rules);

        $campos = $request->all();
        $campos['password'] = bcrypt($request->password);
        $campos['verified'] = User::USUARIO_NO_VERIFICADO; 
        $campos['verification_token'] = User::generarVerificationToken();
        $campos['admin'] = User::USUARIO_REGULAR;

        $user = User::create($campos);

         return $this->showOne($user, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)  //inyeccion del modelo para no usar el ID
    {
        //buscar usuario
         return $this->showOne($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
         //reglas de validacion
        $rules = [
            'email' => 'email|unique:users,email,'.$user->id,
            'password' => '|min:6|confirmed',
            'admin' => 'in: ' . User::USUARIO_ADMINISTRADOR . ',' . User::USUARIO_REGULAR,
        ];

        //validar las reglas
        $this->validate($request,$rules);

        //actualizacion por campo, buscando si existe en la solicitud
        if ($request->has('name')) {
            #//asignar dato a actualizar
            $user->name = $request->name;
        }
        //validar si el email es diferente al existente
        if ($request->has('email') && $user->email != $request->email) {
            
                $user->verification_token = User::generarVerificationToken();
                $user->verified = User::USUARIO_NO_VERIFICADO;
            #//asignar dato a actualizar
            $user->email = $request->email;
        }

         if ($request->has('password')) {
            #//asignar dato a actualizar
            $user->password = bcrypt($request->password);
        }
        if ($request->has('admin')) {
            //valida si es administrador
            $this->allowedAdminAction();
            //si el usuario esta verificado
            if (!$user->esVerificado()) {
                # respuesta de error
                return $this->errorResponse('Error, solo usuario verificado puede cambiar su valor a administrado', 409);
            }
            #//asignar dato a actualizar
            $user->admin = $request->admin;
        }

        //peticiones mal formadas, cuando no se envio nada para actualizar
        if (!$user->isDirty()) {
             return $this->errorResponse('Error, debe enviar por lo menos un valor a actualizar', 422);
        }

        //actualizar informacion
        $user->save(); 

      return $this->showOne($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
         //eliminar
         $user->delete();
         //regresar una respuesta
        return $this->showOne($user);
    }

    //devuelve la informacion del usuario autenticado
    public function me(Request $request)
    {
        $user = $request->user();

        return $this->showOne($user);
    }

    //verifica al usuario
    public function verify($token)
    {
        $user = User::where('verification_token', $token)->firstOrFail();
        $user->verified = User::USUARIO_VERIFICADO;
        $user->verification_token = null;
        $user->save();

        return $this->showMessage('La cuenta ha sido verificada');
    }

    //reenvia un correo al usuario
    public function resend(User $user)
    {
       //validar que no este verificado
        if ($user->esVerificado()) {
            return $this->errorResponse('Este usuario ya ha sido verificado', 409);
        }

        //reenviar el correo
      //funcion retry... sirve para intentar ejecutar una accion varias veces cuando hay un error
        retry(5, function() use ($user) //numero de intentos a realizar
        {  //funcion a ejecutar
            Mail::to($user)->send(new UserCreated($user));
        },200); //tiempo de espera entre intentos    

        //mensaje a mostrar
        return $this->showMessage('El correo se ha reenviado');
    }
}
