<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    //
    // add stocks in all branch and all products

    $branches = Branch::all();
    $products = Product::all();

    foreach ($branches as $branch) {
      foreach ($products as $product) {
        Stock::create([
          'product_id' => $product->id,
          'branch_id' => $branch->id,
          'quantity' => 50,
        ]);
      }
    }
  }
}
