<?php

use Illuminate\Support\Facades\Route;
use Modules\Magnati\Http\Controllers\MagnatiController;

Route::get('magnati/callback', [MagnatiController::class, 'callback'])->name('magnati.callback');
Route::post('magnati/webhook', [MagnatiController::class, 'webhook'])->name('magnati.webhook');
