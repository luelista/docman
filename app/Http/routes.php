<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return redirect()->action('DocumentController@index');
});
Route::get('/documents/{id}/thumbnail', 'DocumentController@thumbnail');
Route::get('/documents/{id}/preview', 'DocumentController@preview');
Route::get('/documents/{id}/raw/{filename}', 'DocumentController@viewFile');
Route::get('/documents/{id}', 'DocumentController@show');
Route::delete('/documents/{id}', 'DocumentController@destroy');
Route::post('/documents/{id}', 'DocumentController@update');
Route::get('/documents', 'DocumentController@index');
Route::post('/documents', 'DocumentController@store');

Route::post('/webHooks/handleMail', 'ImportController@handleMail');

