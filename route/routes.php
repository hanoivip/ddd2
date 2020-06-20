<?php
use Illuminate\Support\Facades\Route;

Route::middleware('web')->namespace('Hanoivip\Ddd2\Controllers')->group(function () {
    Route::get('/login', 'Ddd2@login')->name('login');
    Route::post('/login', 'Ddd2@doLogin')->name('doLogin');
    Route::get('/logout', 'Ddd2@logout')->name('logout');
    Route::get('/logoutsuccess', 'Ddd2@onLogout')->name('logoutsuccess');
    Route::get('/register', 'Ddd2@register')->name('register');
    Route::post('/register', 'Ddd2@doRegister')->name('doRegister');
    Route::get('/password/request', 'Ddd2@forgotPass')->name('password.request');
});