<?php

use App\User;
use App\Product;
use App\Category;
use App\Transaction;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
    	//desactivar la verificacion de claves foraneas
    	DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        //eliminar datos para no tener repetidos
        User::truncate();
        Category::truncate();
        Product::truncate();
        Transaction::truncate();
        DB::table('category_product')->truncate();//tabla pivote

        //deshabilitar los eventos de los modelos, como el de enviar correos
        User::flushEventListeners();
        Category::flushEventListeners();
        Product::flushEventListeners();
        Transaction::flushEventListeners();

        //cantidad de registros a crear
        $cantidadUsuarios = 1000;
        $cantidadCategorias = 30;
        $cantidadProductos = 1000;
        $cantidadTransacciones = 1000;

        //llamada a los factories
        factory(User::class, $cantidadUsuarios)->create();
        factory(Category::class, $cantidadCategorias)->create();
        factory(Product::class, $cantidadTransacciones)->create()->each(
			function ($producto) {
				$categorias = Category::all()->random(mt_rand(1, 5))->pluck('id');
				$producto->categories()->attach($categorias);
			}
		); 
		factory(Transaction::class, $cantidadTransacciones)->create();

    }
}
