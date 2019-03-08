<?php

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->namespace('Hanoivip\Ddd2\Controllers')->group(function () {
    Route::get('/login', function () {
        return view('hanoivip::auth.login');
    })->name('login');
    Route::post('/login', 'Ddd2@login')->name('doLogin');
    Route::get('/logout', function () {
        Cookie::queue(Cookie::forget('ddd2_token'));
        Cookie::queue(Cookie::forget('laravel_session'));
        return response('clean up token & rediect to logout success page');
    })->name('logout');
    Route::get('/logoutsuccess', function () {
        return view('hanoivip::landing');
    })->name('logoutsuccess');
    Route::get('/register', function () {
        return view('hanoivip::auth.register');
    })->name('register');
    Route::get('/password/request', function () {
        return response('Vào app !');
    })->name('password.request');
});