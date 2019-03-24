<?php

use Illuminate\Support\Facades\Route;

Route::middleware('api')->namespace('Hanoivip\Ddd2\Controllers')->prefix('api')->group(function () {
    
    Route::get('/admin/user', 'AdminController@getUserInfo');
    
    Route::get('/admin/pass/reset', 'AdminController@resetPassword');
    
    Route::get('/admin/token', 'AdminController@genToken');
    
});