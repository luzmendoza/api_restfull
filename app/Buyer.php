<?php

namespace App;

use App\Transaction;
use App\Scopes\BuyerScope;
use App\Transformers\BuyerTransformer;

class Buyer extends User
{
	//agregar el transformador de datos de salida
    public $transformer = BuyerTransformer::class;

	protected static function boot()
	{
		parent::boot();
		static::addGlobalScope(new BuyerScope);
	}
	
    //un comprador tiene muchas transacciontes
    public function transactions()
    {
    	return $this->hasMany(Transaction::class);
    }
}
