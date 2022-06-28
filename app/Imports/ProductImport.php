<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductImport implements WithValidation, WithHeadingRow, ToModel
{

  public function model(array $row)
  {

    $product = Product::updateOrCreate(
      ['name' => $row['product_name'], 'type' => $row['type']],
      [
        'name' => $row['product_name'],
        'type' => strtolower($row['type']),
        'price' => $row['price'],
        'default_kg_per_day' => $row['kg_per_day'] ?? 20,
        'reordering_point' => round(($row['kg_per_day'] ?? 20 * 4) + ($row['kg_per_day'] ?? 20 / 4)),
      ]
    );

    if ($product->wasRecentlyCreated) {
      // updateOrCreate performed create
      // create stocks in all branches
      Branch::all()->each(function ($branch) use ($product) {
        Stock::create([
          'branch_id' => $branch->id,
          'product_id' => $product->id,
          'quantity' => 0,
        ]);
      });
    }





    // return $product;
  }
  // public function collection(Collection $rows)
  // {
  //   foreach ($rows as $row) {
  //     Product::create([
  //       'name' => $row['product_name'],
  //       'price' => $row['price'],
  //       'type' => $row['type'],
  //     ]);
  //   }
  // }

  public function rules(): array
  {
    return [
      'product_name' => 'required|string|max:255',
      'type' => 'required|in:chicken,pork,beef,Chicken,Pork,Beef',
      'price' => 'required|numeric',
    ];
  }

  public function headingRow(): int
  {
    return 1;
  }
}
