<?php

namespace App;

use App\Seller;
use App\Category;
use App\Transaction;
use Illuminate\Database\Eloquent\Model;
use App\Transformers\ProductTransformer;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
     use SoftDeletes;

	const PRODUCTO_DISPONIBLE = 'disponible';
	const PRODUCTO_NO_DISPONIBLE = 'no disponible';

    //agregar el transformador de datos de salida
    public $transformer = ProductTransformer::class;

    protected $dates = ['deleted_at'];
    //
    protected $fillable = [
        'name', 
        'description',
        'quantity',
        'status',
        'image',
        'seller_id',
    ];

    //oculta el atributo pivote
    protected $hidden = [
        'pivot'
    ];

    //funcion si el producto esta disponible o no 
    public function estaDisponible()
    {
    	return $this->status == Product::PRODUCTO_DISPONIBLE;
    }

    //muchos a muchos
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    //el producto pertenece a un vendedor
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    //el producto puede tener muchas transacciones
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
