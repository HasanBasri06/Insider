<?php

use App\Jobs\MessageSendJob;
use App\Models\Message;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $message = Message::where('status', 'pending')->first();
    MessageSendJob::dispatch($message->id);
    return view('welcome');
});
