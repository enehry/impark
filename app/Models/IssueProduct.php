<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueProduct extends Model
{
  use HasFactory;

  protected $fillable = [
    'total_price',
    'sold_quantity',
    'stock_id',
    'issue_id',
  ];

  public function issue()
  {
    return $this->belongsTo(Issue::class);
  }

  public function stock()
  {
    return $this->belongsTo(Stock::class);
  }
}
