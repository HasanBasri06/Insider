<?php

use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

Route::controller(MessageController::class)->group(function () {
    Route::get('/messages', 'getAllMessages');
    Route::get('/messages/{id}', 'getMessageById')
        ->where('id', '[0-9]+');
});
