<?php

use App\Http\Controllers\MatchingController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::get('/matching/{hobbies?}',[MatchingController::class,'index'])->name('matching');
Route::view('video-chat', 'video-chat')->name('video-chat');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
