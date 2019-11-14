<?php

namespace App;

use App\Product;
use App\Scopes\SellerScope;
use App\Transformers\SellerTransformer;

class Seller extends User
{
	//agregar el transformador de datos de salida
    public $transformer = SellerTransformer::class;

	protected static function boot()
	{
		parent::boot();
		static::addGlobalScope(new SellerScope);
	}
	
    //un vendendor tiene muchos productos
    public function products()
    {
    	return $this->hasMany(Product::class);
    }
}
