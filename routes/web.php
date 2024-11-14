<?php

use App\Http\Controllers\GeoController;

use Illuminate\Support\Facades\Route;

Route::get('/', [GeoController::class, 'index']);
