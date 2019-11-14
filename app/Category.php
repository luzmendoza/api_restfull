<?php

namespace App;

use App\Product;
use Illuminate\Database\Eloquent\Model;
use App\Transformers\CategoryTransformer;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    //agregar el transformador de datos de salida
    public $transformer = CategoryTransformer::class;

    protected $dates = ['deleted_at'];
    //atributos fillable, llenados de manera masiva
    protected $fillable = [
        'name', 'description', 
    ];

    //oculta el atributo pivote
    protected $hidden = [
        'pivot'
    ];

    //relacion de muchos a muchos
    //belongstomany = pertenece a muchos
    public function products()
    {
    	return $this->belongsToMany(Product::class);
    }
}
