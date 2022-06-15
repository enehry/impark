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
}
