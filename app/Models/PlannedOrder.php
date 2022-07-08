<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlannedOrder extends Model
{
  use HasFactory, SoftDeletes;

  protected $fillable = [
    'stock_id',
    'user_id',
    'branch_id',
    'order_quantity',
    'received_quantity',
    'is_received',
    'issued_at',
    'delivered_at'
  ];

  public function stock()
  {
    return $this->belongsTo(Stock::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function branch()
  {
    return $this->belongsTo(Branch::class);
  }

  public function getIsOrderedAttribute($value)
  {
    return $value ? 'Yes' : 'No';
  }
}
