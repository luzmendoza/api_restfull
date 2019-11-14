<?php 
namespace App\Scopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;
class BuyerScope implements Scope
{
	//modifica una consulta basica y le agrega un has(si tiene)
	public function apply(Builder $builder, Model $model)
	{
		$builder->has('transactions');
	}
} 