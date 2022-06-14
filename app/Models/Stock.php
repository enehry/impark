<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
  use HasFactory;

  protected $fillable = [
    'product_id',
    'branch_id',
    'quantity',
  ];

  public function product()
  {
    return $this->belongsTo(Product::class);
  }

  public function branch()
  {
    return $this->belongsTo(Branch::class);
  }

  public function forecastSetting()
  {
    return $this->hasOne(ForecastSetting::class);
  }
}
