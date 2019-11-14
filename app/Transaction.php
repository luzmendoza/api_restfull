<?php

namespace App;

use App\Buyer;
use Illuminate\Database\Eloquent\Model;
use App\Transformers\TransactionTransformer;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
     use SoftDeletes;

    //agregar el transformador de datos de salida
    public $transformer = TransactionTransformer::class;
     
    protected $dates = ['deleted_at'];
    //
    protected $fillable = [
        'quantity',
        'buyer_id',
        'product_id' 
    ];

    //pertenece a un comprador
    public function buyer()
    {
    	return $this->belongsTo(Buyer::class);
    }

    //pertenece a un producto
    public function product()
    {
    	return $this->belongsTo(Product::class);
    }
}
