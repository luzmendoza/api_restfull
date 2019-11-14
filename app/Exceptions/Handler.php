<?php

namespace App\Exceptions;

use Exception;
use App\Traists\ApiResponser;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    use ApiResponser;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        //validacion de una excepcion, un error
        if ($exception instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($exception, $request);
        } 

        //validacion cuando no se encuentra un objeto
        if ($exception instanceof ModelNotFoundException) {
            $modelo = strtolower(class_basename($exception->getModel())); //obtiene el modelo que no esta
            return $this->errorResponse("No existe ninguna instancia de {$modelo} con el id especificado", 404);
        }

        //validacion cuando no se esta autenticado en el sistema.... sera necesario en la version actual ?
        if ($exception instanceof AuthenticationException) {
            return $this->unauthenticated($request, $exception);
        }

        //validacion de autorizacion
        if ($exception instanceof AuthorizationException) {
            return $this->errorResponse('No posee permisos para ejecutar esta acción', 403);
        }

        //validar cuando la ruta no exista
        if ($exception instanceof NotFoundHttpException) {
            return $this->errorResponse('No se encontró la URL especificada', 404);
        }

        //valida cuando se hace un llamado a un metodo invalido
         if ($exception instanceof MethodNotAllowedHttpException) {
            return $this->errorResponse('El método especificado en la petición no es válido', 405);
        }

        //validar diferentes excepciones del http
        if ($exception instanceof HttpException) {
            return $this->errorResponse($exception->getMessage(), $exception->getStatusCode());
        }

        //validar cuando se quiere eliminar un objeto relacionado con otra tabla
        if ($exception instanceof QueryException) {
            $codigo = $exception->errorInfo[1];
            if ($codigo == 1451) {
                return $this->errorResponse('No se puede eliminar de forma permamente el recurso porque está relacionado con algún otro.', 409);
            }
        }

        //return parent::render($request, $exception);
        //fallas inesperadas del sistema, si se cae la bd o algo asi
        if (config('app.debug')) {
            return parent::render($request, $exception);            
        }
        return $this->errorResponse('Falla inesperada. Intente luego', 500);
    }

    //respuesta de las excepciones
     protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $errors = $e->validator->errors()->getMessages();
        return $this->errorResponse($errors, 422);
    }

    //respuesta de la validacion de autenticacion
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $this->errorResponse('No autenticado.', 401);        
    }
}
