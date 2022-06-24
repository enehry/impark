<?php

namespace App\Console;

use App\Http\Controllers\LogHelper;
use App\Models\ForecastSetting;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
  /**
   * Define the application's command schedule.
   *
   * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
   * @return void
   */
  protected function schedule(Schedule $schedule)
  {
    // $schedule->command('inspire')->hourly();
    $schedule->call(function () {
      // get all issue_products of all branches group by stock_id and branch_id sum the sold_quantity
      $total_sold_quantity = DB::table('issue_products')
        ->select(
          'issue_products.stock_id',
          DB::raw('SUM(issue_products.sold_quantity) as sold_quantity')
        )
        ->selectRaw('IFNULL(forecast_settings.reordering_point, products.reordering_point) as reordering_point')
        ->selectRaw('IFNULL(forecast_settings.default_kg_per_day, products.default_kg_per_day) as default_kg_per_day')
        ->groupBy('issue_products.stock_id')
        ->leftJoin('forecast_settings', 'issue_products.stock_id', '=', 'forecast_settings.stock_id')
        ->leftJoin('stocks', 'issue_products.stock_id', '=', 'stocks.id')
        ->leftJoin('products', 'stocks.product_id', '=', 'products.id')
        ->whereDate('issue_products.created_at', '=', date('Y-m-d'))
        ->get();

      // loop through all stocks and update the forecast_default kg per day if the total sold
      // is greater than the forecast_default kg per day
      foreach ($total_sold_quantity as $stock) {
        // kg/per day 45,  nabenta today 49, = 50 = kg/day update
        // kg/per day 45, nabenta 44, = 43 = kg/day update
        $kg_per_day = $stock->default_kg_per_day;
        if ($stock->sold_quantity > $kg_per_day) {
          $kg_per_day++;
        } else if ($stock->sold_quantity < $kg_per_day) {
          $kg_per_day--;
        }
        // update if exist if not create
        ForecastSetting::updateOrCreate(
          ['stock_id' => $stock->stock_id],
          ['reordering_point' => $stock->reordering_point, 'default_kg_per_day' => $kg_per_day],
        );
      }

      LogHelper::log('Forecast Settings Updated', 'Automatic Forecast Settings Updated', 'forecast_settings', []);
    });
  }

  /**
   * Register the commands for the application.
   *
   * @return void
   */
  protected function commands()
  {
    $this->load(__DIR__ . '/Commands');

    require base_path('routes/console.php');
  }
}
