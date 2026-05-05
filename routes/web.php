<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/tutorial-pengajuan', function () {
    return view('tutorial-pengajuan');
})->name('tutorial.pengajuan');
