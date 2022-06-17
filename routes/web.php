<?php

use App\Http\Controllers\BOMController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\IssueProductController;
use App\Http\Controllers\ForecastingController;
use App\Http\Controllers\PlannedOrderController;
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

    // planned orders
    Route::resource('/planned-orders', PlannedOrderController::class);
    Route::post('/planned-orders-convert', [PlannedOrderController::class, 'deliver'])->name('planned-orders.convert');
    Route::delete('/planned-orders-trash/{id}', [PlannedOrderController::class, 'trash'])->name('planned-orders.trash');
    Route::post('/planned-orders-cancel-all', [PlannedOrderController::class, 'cancelAll'])->name('planned-orders.cancel-all');
    Route::get('/planned-orders-all-trashed', [PlannedOrderController::class, 'allTrashed'])->name('planned-orders.all-trashed');
    Route::post('/planned-orders-restore-all', [PlannedOrderController::class, 'restoreAll'])->name('planned-orders.restore-all');
    Route::post('/planned-orders-restore/{id}', [PlannedOrderController::class, 'restore'])->name('planned-orders.restore');
  });

  Route::middleware('user')->group(function () {
    Route::get('/stocks', [StockController::class, 'index'])->name('stocks.index');
    Route::post('/stocks/update-forecast-setting', [StockController::class, 'updateOrCreateForecastSetting'])->name('stocks.forecast.update');
    Route::get('/stocks/export-pdf', [StockController::class, 'exportPDF'])->name('stocks.export.pdf');
    Route::get('/stocks/export-excel', [StockController::class, 'exportExcel'])->name('stocks.export.excel');
    Route::resource('/issue-products', IssueProductController::class);
    Route::post('/issue-products-proceed', [IssueProductController::class, 'proceedTransaction'])->name('issue-products.proceed');
    Route::get('/issue-download-pdf', [IssueProductController::class, 'receiptIssueProduct'])->name('issue-products.pdf');
    Route::resource('/forecasting', ForecastingController::class);
    Route::post('/confirm-forecast', [ForecastingController::class, 'confirmForecast'])->name('forecast.confirm');
  });

  // Route::get('/', function () {
  //   return Inertia::render('Menu');
  // })->name('Menu');




});
