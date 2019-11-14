<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\User;
use App\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
     return [
        'name' => $faker->word,
        'description' => $faker->paragraph(1),
        'quantity' => $faker->numberBetween(1, 10),
        'status' => $faker->randomElement([Product::PRODUCTO_DISPONIBLE, Product::PRODUCTO_NO_DISPONIBLE]),
        'image' => $faker->randomElement(['1.jpg', '2.jpg', '3.jpg']),
        'seller_id' => User::all()->random()->id,
    ];
});
