<?php

namespace Database\Seeders;

use App\Models\Stock;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SalesSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    // generate random data for issue product


    // get all stocks

    $stocks =  Stock::All();

    foreach ($stocks as $stock) {
      //get product price
      $price = $stock->product->price;
      $rand_quantity = rand(1, 100);
      $stock->issueProduct()->create([
        'sold_quantity' => $rand_quantity,
        'total_price' => $price * $rand_quantity,
        'issue_id' => rand(1, 10),
        // random month and day of year 2022 and not greater than current date
        // 'created_at' => now()->subMonths(rand(1, 12))->subDays(rand(1, 28)),
        // 'updated_at' => now()->subMonths(rand(1, 12))->subDays(rand(1, 28)),
        // sales for today
        'created_at' => now(),
        'updated_at' => now(),
      ]);
    }
  }
}
