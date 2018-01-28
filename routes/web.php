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
Route::view('/allabout', 'posts/about');

Route::group(['prefix' => '/admin/index'], function($router) {
     $router->get('/', 'PostsController@adminIndex');
     $router->post('/post', 'PostsController@authPost');
 });

Route::group(['prefix' => '/posts'], function($router) {
    $router->post('/', 'PostsController@post');
    $router->get('/complete', 'PostsController@store');
    $router->get('/del/{id}', 'PostsController@destroy');
    $router->get('/search', 'PostsController@search');
    $router->get('/sort/comments', 'CommentsController@sortByComment');
    $router->get('/sort/lastupdated', 'PostsController@sortByLastUpdated');
    $router->get('/{id}', 'PostsController@edit');
    $router->post('/update/{id}', 'PostsController@update');
    $router->get('/del/image/{image_id}', 'ImagesController@destroy');
    $router->get('/show/{id}', 'PostsController@show');
    $router->post('/{id}/comments', 'CommentsController@store');
    $router->get('/comments/{comment_id}', 'CommentsController@destroy');
});

Auth::routes();
Route::get('/user/verify/{token}', 'Auth\RegisterController@verifyUser');
Route::get('/home', 'HomeController@index')->name('home');
