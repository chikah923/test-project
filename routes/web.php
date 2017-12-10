<?php

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

Route::get('/', 'PostsController@index');
Route::post('/posts', 'PostsController@store');
Route::get('/posts/del/{post}', 'PostsController@destroy');
Route::get('/posts/{post}', 'PostsController@edit');
Route::post('/posts/update/{post}', 'PostsController@update');
