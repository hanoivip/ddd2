<?php
use Illuminate\Support\Facades\Route;

Route::middleware('api')->namespace('Hanoivip\Ddd2\Controllers')
    ->prefix('api')
    ->group(function () {
    Route::get('/admin/user', 'AdminController@getUserInfo');
    Route::get('/admin/pass/reset', 'AdminController@resetPassword');
    Route::get('/admin/token', 'AdminController@genToken');
});

Route::middleware('api')->namespace('Hanoivip\Ddd2\Controllers')
    ->prefix('api')
    ->group(function () {
        Route::any('/login', 'Ddd2@doLogin');
        Route::any('/userinfo', 'Ddd2@doGetInfo');
        Route::any('/register', 'Ddd2@doRegister');
        Route::any('/logout', 'Ddd2@logout');
    });