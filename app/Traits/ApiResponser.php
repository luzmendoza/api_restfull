<?php
namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;


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

		//llamar al filtrado de datos
		$collection = $this->filterData($collection, $transformer);
		//llamar al metodo de ordenamiento
		$collection = $this->sortData($collection, $transformer);
		//aplicar paginacion a los resultados
		$collection = $this->paginate($collection);
		//llamar al metodo de transformacion
		$collection = $this->transformerData($collection, $transformer);
		//mantener datos en cache para evitar llamados a la base de datos
		$collection = $this->cacheResponse($collection);

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
	protected function sortData(Collection $collection, $transformer)
	{
		//ordenar si se recibe en la peticion
		if (request()->has('sort_by')) {
			//obtener el atributo con el que se ordenara
			$attribute = $transformer::originalAttribute(request()->sort_by);
			//realizar el ordenamiento
			$collection = $collection->sortBy($attribute);
		}
		return $collection;
	}

	//paginacion
	protected function paginate(Collection $collection)
	{
		//reglas de paginacion
		$rules = [
			'per_page' => 'integer|min:2|max:50'
		];
		//validar las reglas
		Validator::validate(request()->all(), $rules);
		//resolver la pagina actual
		$page = LengthAwarePaginator::resolveCurrentPage();
		//valores por pagina
		$perPage = 15;
		if (request()->has('per_page')) {
			$perPage = (int)request()->per_page;
		}
		//dividir la coleccion en secciones
		$results = $collection->slice(($page - 1) * $perPage, $perPage)->values();
		//realizar la paginacion
		$paginated = new LengthAwarePaginator($results, $collection->count(), $perPage, $page, ['path' => LengthAwarePaginator::resolveCurrentPage(),]);
		//agregar datos eliminados en la paginacion
		$paginated->appends(request()->all());		

		//regresar datos paginados
		return $paginated;
	}

	//metodo para transformar los datos del modelo a una salida customizada
	protected function transformerData($data, $transformer)
	{
		//construir la transformacion mediante fractal
		$transformation = fractal($data, new $transformer);
		//regresar de modo array entendido por php
		return $transformation->toArray();
	}

	protected function filterData(Collection $collection, $transformer)
	{
		foreach (request()->query() as $query => $value) {
			# 
			$attribute = $transformer::originalAttribute($query);

			if (isset($attribute, $value)) {
				# code...
				$collection = $collection->where($attribute,$value);
			}
		}

		return $collection;
	}

	//mantener datos en cache
	protected function cacheResponse($data)
	{
		//conocer la url actual
		$url = request()->url();
		//obtener los parametros de la url
		$queryParams = request()->query();
		//ordenar los parametros de la url
		ksort($queryParams);
		//construir un query
		$queryString = http_build_query($queryParams);
		//construir url completa.. para usarla para ver cuando guardar o no el cache
		$fullUrl = "{$url}?{$queryString}";
		//usar el facade cache       tiempo en segundo (15 segundos)
		return Cache::remember($fullUrl, 30, function() use($data){
			return $data;
		});

	}
}
?>