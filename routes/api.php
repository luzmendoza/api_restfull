<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

//ruta sin seguridad, pero bloqueando las rutas que se pueden ver, aqui solo se pueden ver iindex y show
Route::resource('buyers','Buyer\BuyerController', ['only'=> ['index', 'show']]);
Route::resource('buyers.transactions','Buyer\BuyerTransactionController', ['only'=> ['index']]);
Route::resource('buyers.products','Buyer\BuyerProductController', ['only'=> ['index']]);
Route::resource('buyers.sellers','Buyer\BuyerSellerController', ['only'=> ['index']]);
Route::resource('buyers.categories','Buyer\BuyerCategoryController', ['only'=> ['index']]);
//sellers
Route::resource('sellers','Seller\SellerController', ['only'=> ['index', 'show']]);
Route::resource('sellers.transactions','Seller\SellerTransactionController', ['only'=> ['index']]);
Route::resource('sellers.categories','Seller\SellerCategoryController', ['only'=> ['index']]);
Route::resource('sellers.buyers','Seller\SellerBuyerController', ['only'=> ['index']]);
Route::resource('sellers.products','Seller\SellerProductController', ['except'=> ['create','show','edit']]);
//categorias
Route::resource('categories','Category\CategoryController', ['except'=> ['create', 'edit']]);
Route::resource('categories.products','Category\CategoryProductController', ['only'=> ['index']]);
Route::resource('categories.sellers','Category\CategorySellerController', ['only'=> ['index']]);
Route::resource('categories.transactions','Category\CategoryTransactionController', ['only'=> ['index']]);
Route::resource('categories.buyers','Category\CategoryBuyerController', ['only'=> ['index']]);
//products
Route::resource('products','Product\ProductController', ['only'=> ['index', 'show']]);
Route::resource('products.transactions','Product\ProductTransactionController', ['only'=> ['index']]);
Route::resource('products.buyers','Product\ProductBuyerController', ['only'=> ['index']]);
Route::resource('products.categories','Product\ProductCategoryController', ['only'=> ['index', 'update', 'destroy']]);
Route::resource('products.buyers.transactions','Product\ProductBuyerTransactionController', ['only'=> ['store']]);
//transaction
Route::resource('transactions','Transaction\TransactionController', ['only'=> ['index', 'show']]);
Route::resource('transactions.categories','Transaction\TransactionCategoryController', ['only'=> ['index']]);
Route::resource('transactions.sellers', 'Transaction\TransactionSellerController', ['only' => ['index']]);

//regresar informacion del usuario que esta actualmente autenticado -users/me
Route::name('me')->get('users/me','User\UserController@me');
//user... muestra todas las rutas excepto crear y editar, los cuales son formularios, pero se realizara la accion de crear y editar a traves de los metdos pot, update, delete
Route::resource('users','User\UserController', ['except'=> ['create', 'edit']]);
//verificar y reenviar un token
Route::name('verify')->get('users/verify/{token}','User\UserController@verify');
Route::name('resend')->get('users/{user}/resend','User\UserController@resend');

//registrar ruta de oauth toke para dejar de usar el trhout middleware
Route::post('oauth/token','\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');