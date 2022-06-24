<?php

use App\Http\Controllers\AgeReportController;
use App\Http\Controllers\BOMController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DistributeReceivableController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\IssueProductController;
use App\Http\Controllers\ForecastingController;
use App\Http\Controllers\InventoryReportController;
use App\Http\Controllers\PlannedOrderController;
use App\Http\Controllers\ReceiveProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\UserLogController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
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

    // products
    Route::resource('/products', ProductController::class);
    Route::get('/products-excel', [ProductController::class, 'downloadExcel'])->name('products.excel');
    Route::get('/products-pdf', [ProductController::class, 'downloadPDF'])->name('products.pdf');

    Route::get('/bom', [BOMController::class, 'index'])->name('bom.index');
    Route::put('/bom/update-price', [BOMController::class, 'updatePrice'])->name('bom.update-price');

    // planned orders
    Route::resource('/planned-orders', PlannedOrderController::class);
    Route::delete('/planned-orders-delete/{id}', [PlannedOrderController::class, 'delete'])->name('planned-orders.delete');
    Route::post('/planned-orders-convert', [PlannedOrderController::class, 'deliver'])->name('planned-orders.convert');
    Route::delete('/planned-orders-trash/{id}', [PlannedOrderController::class, 'trash'])->name('planned-orders.trash');
    Route::post('/planned-orders-cancel-all', [PlannedOrderController::class, 'cancelAll'])->name('planned-orders.cancel-all');
    Route::get('/planned-orders-all-trashed', [PlannedOrderController::class, 'allTrashed'])->name('planned-orders.all-trashed');
    Route::post('/planned-orders-restore-all', [PlannedOrderController::class, 'restoreAll'])->name('planned-orders.restore-all');
    Route::delete('/planned-orders-delete-all', [PlannedOrderController::class, 'deleteAll'])->name('planned-orders.delete-all');
    Route::post('/planned-orders-restore/{id}', [PlannedOrderController::class, 'restore'])->name('planned-orders.restore');

    // distribute receivable
    Route::get('/distribute-receivables', [DistributeReceivableController::class, 'index'])->name('distribute-receivables.index');
    Route::post('/distribute-receivables', [DistributeReceivableController::class, 'proceed'])->name('distribute-receivables.proceed');

    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions-download-excel', [TransactionController::class, 'downloadExcel'])->name('transactions.download-excel');
    Route::get('/transactions-download-pdf', [TransactionController::class, 'downloadPDF'])->name('transactions.download-pdf');

    Route::post('bypass-forecasting', [ForecastingController::class, 'bypassNextDay'])->name('forecasting.bypass');
    // reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    // Inventory reports
    Route::get('inventory-report', [InventoryReportController::class, 'index'])->name('inventory-report.index');
    Route::get('inventory-chart', [InventoryReportController::class, 'chart'])->name('inventory-report.chart');
    Route::get('inventory-line-chart', [InventoryReportController::class, 'lineChart'])->name('inventory-report.line-chart');
    Route::get('inventory-report-excel', [InventoryReportController::class, 'downloadExcel'])->name('inventory-report.excel');
    Route::get('inventory-report-pdf', [InventoryReportController::class, 'downloadPDF'])->name('inventory-report.pdf');

    // Sales reports
    Route::get('sales-report', [SalesReportController::class, 'index'])->name('sales-report.index');
    Route::get('sales-report-excel', [SalesReportController::class, 'downloadExcel'])->name('sales-report.excel');
    Route::get('sales-report-pdf', [SalesReportController::class, 'downloadPDF'])->name('sales-report.pdf');
    Route::get('sales-historical-data', [SalesReportController::class, 'historicalData'])->name('sales-report.historical-data');
    Route::get('sales-historical-data-excel', [SalesReportController::class, 'downloadHistoricalDataExcel'])->name('sales-report.historical-data-excel');
    Route::get('sales-historical-data-pdf', [SalesReportController::class, 'downloadHistoricalDataPDF'])->name('sales-report.historical-data-pdf');
    Route::get('sales-report-chart', [SalesReportController::class, 'chart'])->name('sales-report.chart');

    // Age report
    Route::get('age-report', [AgeReportController::class, 'index'])->name('age-report.index');
    Route::get('age-report-excel', [AgeReportController::class, 'downloadExcel'])->name('age-report.excel');
    Route::get('age-report-pdf', [AgeReportController::class, 'downloadPDF'])->name('age-report.pdf');
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

    Route::get('/receive-products', [ReceiveProductController::class, 'index'])->name('receive-products.index');
    Route::get('/receive-products/logs', [ReceiveProductController::class, 'ReceivedProductLogs'])->name('receive-products-logs.index');
    Route::get('/received-products-download-pdf', [ReceiveProductController::class, 'downloadPDF'])->name('received-products.pdf');
    Route::get('/received-products-download-excel', [ReceiveProductController::class, 'downloadExcel'])->name('received-products.excel');
    Route::post('/receive-products-received', [ReceiveProductController::class, 'receiveProducts'])->name('receive-products.received');

    // reports
    Route::get('/reports-branch', [ReportController::class, 'indexBranch'])->name('report-branch.index');
    Route::get('/inventory-report-branch', [InventoryReportController::class, 'indexBranch'])->name('inventory-report-branch.index');
    Route::get('/inventory-report-branch-excel', [InventoryReportController::class, 'downloadBranchExcel'])->name('inventory-report-branch.excel');
    Route::get('/inventory-report-branch-pdf', [InventoryReportController::class, 'downloadBranchPDF'])->name('inventory-report-branch.pdf');
    Route::get('/inventory-report-branch-chart', [InventoryReportController::class, 'chartBranch'])->name('inventory-report-branch.chart');
    Route::get('/inventory-report-branch-line-chart', [InventoryReportController::class, 'lineChartBranch'])->name('inventory-report-branch.line-chart');

    // sales
    Route::get('/sales-report-branch', [SalesReportController::class, 'indexBranch'])->name('sales-report-branch.index');
    Route::get('/sales-report-branch-excel', [SalesReportController::class, 'downloadBranchExcel'])->name('sales-report-branch.excel');
    Route::get('/sales-report-branch-pdf', [SalesReportController::class, 'downloadBranchPDF'])->name('sales-report-branch.pdf');
    route::get('/sales-historical-data-branch-index', [SalesReportController::class, 'historicalDataIndex'])->name('sales-report-branch.historical-index');
    route::get('/sales-historical-data-branch-excel', [SalesReportController::class, 'downloadHistoricalDataBranchExcel'])->name('sales-report-branch.historical-excel');
    route::get('/sales-historical-data-branch-pdf', [SalesReportController::class, 'downloadHistoricalDataBranchPDF'])->name('sales-report-branch.historical-pdf');
    Route::get('/sales-report-branch-chart', [SalesReportController::class, 'chartBranch'])->name('sales-report-branch.chart');

    // age report
    Route::get('/age-report-branch', [AgeReportController::class, 'indexBranch'])->name('age-report-branch.index');
    Route::get('/age-report-branch-excel', [AgeReportController::class, 'downloadAgeReportBranchExcel'])->name('age-report-branch.excel');
    Route::get('/age-report-branch-pdf', [AgeReportController::class, 'downloadAgeReportBranchPDF'])->name('age-report-branch.pdf');
  });

  // shared components
  Route::get('user-logs', [UserLogController::class, 'index'])->name('user-logs.index');
  Route::get('user-logs/export-excel', [UserLogController::class, 'downloadExcel'])->name('user-logs.download.excel');
  Route::get('user-logs/export-pdf', [UserLogController::class, 'downloadPDF'])->name('user-logs.download.pdf');

  // Route::get('/', function () {
  //   return Inertia::render('Menu');
  // })->name('Menu');




});
