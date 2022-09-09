<?php
use Illuminate\Support\Facades\Route;

Route::middleware('web')->namespace('Hanoivip\Ddd2\Controllers')->group(function () {
    Route::get('/login', 'Ddd2@login')->name('login');
    Route::post('/login', 'Ddd2@doLogin')->name('doLogin');
    Route::get('/login/success', 'Ddd2@onLoginSuccess')->name('login-success');
    Route::any('/logout', 'Ddd2@logout')->name('logout');
    Route::get('/logout/success', 'Ddd2@onLogout')->name('logout-success');
    Route::get('/register', 'Ddd2@register')->name('register');
    Route::post('/register', 'Ddd2@doRegister')->name('doRegister');
    Route::get('/register/success', 'Ddd2@onRegisterSuccess')->name('register-success');
    Route::get('/password/request', 'Ddd2@forgotPass')->name('password.request');
});