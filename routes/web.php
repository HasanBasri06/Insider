<?php

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    Redis::set('test_key_123', 'hello');
    return view('welcome');
});
