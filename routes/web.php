<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('livewire.frontend.home-page');
});

