<?php
namespace App\Traists;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;


trait ApiResponser
{
	//respuestas satisfactorias
	private function successResponse($data, $code)
	{
		return response()->json($data, $code);
	}

	//respuestas con error
	protected function errorResponse($message, $code)
	{
		return response()->json(['error' => $message, 'code' => $code], $code);
	}

	//mostrar todos los elementos de una tabla
	protected function showAll(Collection $collection, $code = 200)
	{
		//validar que la coleccion tenga datos antes de transformar
		if ($collection->isEmpty()) {
			return $this->successResponse(['data' => $collection], $code);
		}
		//indicar que tipo de transformacion se hara
		$transformer = $collection->first()->transformer;
		//llamar al metodo de ordenamiento
		$collection = $this->sortData($collection);
		//llamar al metodo de transformacion
		$collection = $this->transformerData($collection, $transformer);

		return $this->successResponse($collection, $code);
	}

	//mostrar un elemento de una tabla
	protected function showOne(Model $instance, $code = 200)
	{
		//indicar que tipo de transformacion se hara
		$transformer = $instance->transformer;
		//llamar al metodo de transformacion
		$instance = $this->transformerData($instance, $transformer);

		return $this->successResponse($instance, $code);
	}

	//mostrar un elemento de una tabla
	protected function showMessage($message, $code = 200)
	{
		return $this->successResponse(['data' => $message], $code);
	}

	//ordenamiento de datos
	protected function sortData(Collection $collection)
	{
		//ordenar si se recibe en la peticion
		if (request()->has('sort_by')) {
			//obtener el atributo con el que se ordenara
			$attribute = request()->sort_by;
			//realizar el ordenamiento
			$collection = $collection->sortBy($attribute);
		}
		return $collection;
	}

	//metodo para transformar los datos del modelo a una salida customizada
	protected function transformerData($data, $transformer)
	{
		//construir la transformacion mediante fractal
		$transformation = fractal($data, new $transformer);
		//regresar de modo array entendido por php
		return $transformation->toArray();
	}
}
?>