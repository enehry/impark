<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductImport implements ToModel, WithValidation, WithHeadingRow
{
  /**
   * @param Collection $collection
   */
  public function model(array $row)
  {
    //
    $product = Product::create([
      'name' => $row['name'] ?? $row['product_name'] ?? $row['PRODUCT_NAME'] ?? $row['NAME'],
      'price' => $row['price'] ?? $row['PRICE'] ?? $row['PRODUCT_PRICE'],
      'type' => $row['type'] ?? $row['PRODUCT_TYPE'] ?? $row['TYPE'],
    ]);

    if ($product) {
      // create stocks in all branches
      Branch::all()->each(function ($branch) use ($product) {
        Stock::create([
          'branch_id' => $branch->id,
          'product_id' => $product->id,
          'quantity' => 0,
        ]);
      });
    }

    return $product;
  }

  public function rules(): array
  {
    return [
      'product_name' => 'required|unique:products,name',
      'price' => 'required|numeric',
      'type' => 'required|in:chicken,pork,beef',
    ];
  }

  public function headingRow(): int
  {
    return 1;
  }
}
