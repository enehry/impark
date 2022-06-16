<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'price',
    'type,',
    'reordering_point',
    'maximum_shelf_life',
    'default_kg_per_day',
  ];

  public function stocks()
  {
    return $this->hasMany(Stock::class);
  }
}
