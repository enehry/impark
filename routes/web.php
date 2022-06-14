<?php

use App\Http\Controllers\BOMController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
  return Inertia::render('Auth/Login',);
});

Route::middleware([
  'auth:sanctum',
  config('jetstream.auth_session'),
  'verified',
])->group(function () {
  Route::get('/', [MenuController::class, 'index'])->name('Menu');

  Route::middleware('admin')->group(function () {
    Route::resource('/branch', BranchController::class);
    Route::resource('/users', UserController::class);
    Route::resource('/products', ProductController::class);
    Route::get('/bom', [BOMController::class, 'index'])->name('bom.index');
    Route::put('/bom/update-price', [BOMController::class, 'updatePrice'])->name('bom.update-price');
  });

  Route::middleware('user')->group(function () {
  });

  // Route::get('/', function () {
  //   return Inertia::render('Menu');
  // })->name('Menu');




});
