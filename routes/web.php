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
Route::get('/posts/del/{id}', 'PostsController@destroy');
Route::get('/posts/{id}', 'PostsController@edit');
Route::post('/posts/update/{id}', 'PostsController@update');
Route::get('/posts/show/{id}', 'PostsController@show');
Route::post('posts/{id}/comments', 'CommentsController@store');
Route::get('/posts/comments/{comment_id}', 'CommentsController@destroy');
