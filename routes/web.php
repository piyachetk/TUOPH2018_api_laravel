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

Route::get('/', function(){
    return view('index');
});
Route::get('/admin/login', function(){
    return view('login');
});
Route::post('/admin/login', 'AdminController@login');
Route::group([ 'middleware' => ['auth']], function(){
    Route::get('/admin', function () {
        return view('admin');
    });
    Route::get('/admin/check', function () {
        return view('admin-searchUser');
    });
    Route::get('/admin/user', function () {
        return view('admin-userInfo');
    });
    Route::get('/admin/edit', function () {
        return view('admin-editUser');
    });
    Route::post('/admin/edit', 'AdminController@dirtySetData');
    Route::get('/logout', function () {
        session()->flush();
        return redirect('/');
    });
});
Route::get('/api/token', 'AccountController@getAccessToken');
Route::get('/api/me', 'AccountController@me');
Route::post('/api/register', 'AccountController@register');
Route::get('/api/admin', 'AccountController@getBoothForAdmin');
Route::post('/api/admin/edit', 'AccountController@editAdmin');
Route::get('/api/booths', 'AccountController@getAllBooths');
Route::get('/api/tags', 'AccountController@getAllTags');
Route::post('/api/scan', 'AccountController@scan');