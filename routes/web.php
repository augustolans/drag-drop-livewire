<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\SortableList;


Route::get('/', function () {
    return view('welcome');
});


Route::get('/tasks', function () {
    return view('tasks');
});

