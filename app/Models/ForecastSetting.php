<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForecastSetting extends Model
{
  use HasFactory;

  protected $fillable = [
    'stock_id',
    'reordering_point',
    'default_kg_per_day',
  ];
}
