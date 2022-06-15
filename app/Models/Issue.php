<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
  use HasFactory;

  protected $fillable = [
    'user_id',
    'branch_id',
    'sum_total_price',
  ];


  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function branch()
  {
    return $this->belongsTo(Branch::class);
  }

  public function issueProducts()
  {
    return $this->hasMany(IssueProduct::class);
  }
}
