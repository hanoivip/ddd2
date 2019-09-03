<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->namespace('Hanoivip\Ddd2\Controllers')->group(function () {
    Route::get('/login', function () {
        if (Auth::check())
            return redirect('/');
        return view('hanoivip::auth.login');
    })->name('login');
    Route::post('/login', 'Ddd2@login')->name('doLogin');
    Route::get('/logout', function () {
        if (!Auth::check())
            return redirect('/');
        Cookie::queue(Cookie::forget('ddd2_token'));
        Cookie::queue(Cookie::forget('laravel_session'));
        return view('hanoivip::landing');
    })->name('logout');
    Route::get('/logoutsuccess', function () {
        return view('hanoivip::landing');
    })->name('logoutsuccess');
    Route::get('/register', function () {
        return view('hanoivip::auth.register');
    })->name('register');
    Route::get('/password/request', function () {
        return response('Chỉ hỗ trợ tìm lại mật khẩu từ App!');
    })->name('password.request');
});