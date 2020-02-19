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
Route::get('/documents/list_selected', 'DocumentController@listSelected');
//Route::get('/documents/{id}/thumbnail/{page}', 'DocumentController@thumbnail');
//Route::get('/documents/{id}/preview/{page}', 'DocumentController@preview');
Route::get('/documents/{id}/raw/{filename}', 'DocumentController@viewFile');
Route::get('/documents/{id}', 'DocumentController@show');
Route::get('/documents/{id}/edit', 'DocumentController@edit');
Route::post('/documents/{id}/splitPdf', 'DocumentController@splitPdf');
Route::post('/documents/{id}/updatePreview', 'DocumentController@updatePreview');
Route::delete('/documents/{id}', 'DocumentController@destroy');
Route::post('/documents/{id}', 'DocumentController@update');
Route::get('/documents', 'DocumentController@index');
Route::post('/documents', 'DocumentController@store');
Route::get('/tags', 'DocumentController@allTags');
Route::get('/imports', 'DocumentController@importEditor');
Route::get('/updatetags', 'DocumentController@updateTags');

Route::get('/documents/{id}_{token}/log', 'ImportController@fetchLog');
Route::post('/imports/update', 'ImportController@massUpdate');

Route::post('/webHooks/handleMail', 'ImportController@handleMail');
Route::put('/webHooks/handleFtp', 'ImportController@handleFtp');

Route::get('/shared/{id}_{token}', 'DocumentController@showShareLink');
Route::get('/shared/{id}_{token}/thumbnail/{page}', 'DocumentController@thumbnail');
Route::get('/shared/{id}_{token}/preview/{page}', 'DocumentController@preview');

