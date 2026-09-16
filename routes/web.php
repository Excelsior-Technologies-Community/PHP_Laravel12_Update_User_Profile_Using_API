<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('api-console');
});

Route::get('/api-console', function () {
    return view('api-console');
});

