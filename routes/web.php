<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['namespace' => 'Web'], function (Router $router) {
    $router->get('/', 'IndexController@index');
    $router->get('/getWallPaperList', 'IndexController@getWallPaperList');
    $router->post('/changeSource', 'IndexController@changeSource');
});
