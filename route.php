<?php

use \App\Facades\Route;

Route::get('node', '\App\Http\Controller\NodeController', 'paginate');
Route::get('node/{id}', '\App\Http\Controller\NodeController', 'find');
Route::delete('node/{id}', '\App\Http\Controller\NodeController', 'delete');
Route::post('node', '\App\Http\Controller\NodeController', 'create');
Route::put('node/{id}', '\App\Http\Controller\NodeController', 'update');

Route::get('nav', '\App\Http\Controller\NavController', 'paginate');
Route::get('nav/{id}', '\App\Http\Controller\NavController', 'find');
Route::delete('nav/{id}', '\App\Http\Controller\NavController', 'delete');
Route::post('nav', '\App\Http\Controller\NavController', 'create');
Route::put('nav/{id}', '\App\Http\Controller\NavController', 'update');

Route::get('relation/nav/{id}', '\App\Http\Controller\ExampleController', 'nav');
Route::get('relation/node/{id}', '\App\Http\Controller\ExampleController', 'node');
Route::post('relation/create', '\App\Http\Controller\ExampleController', 'create');
Route::delete('relation/delete/{id}', '\App\Http\Controller\ExampleController', 'delete');

Route::post('load/init', '\App\Http\Controller\LoadController', 'init');


