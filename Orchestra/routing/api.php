<?php

namespace Orchestra\routing;

include 'Route.php';

/**
 * --------------------------------------------
 * API Middlewares
 * --------------------------------------------
 * 
 * Here you can define middlewares for your api. These middlewares
 * will be loaded by then RouteProvider and all of them will be accessible
 * through the /middleware/endpoint prefixes
 */
Route::middleware('auth')->get('/login');
Route::middleware('auth')->get('/register');
Route::middleware('auth')->get('/reset');
Route::middleware('auth')->get('/otp');

Route::middleware('student')->get('/onboard');
