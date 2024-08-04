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
Route::middleware('test')->get('/test');
Route::middleware('test')->get('/edit');
Route::middleware('test')->get('/delete');
Route::middleware('test')->get('/create');
