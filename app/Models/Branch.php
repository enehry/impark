<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'address',
    'description',
  ];

  public function products()
  {
    return $this->hasMany(Product::class);
  }

  public function users()
  {
    return $this->hasMany(User::class);
  }

  public function userLogs()
  {
    return $this->hasMany(UserLog::class);
  }

  public function stocks()
  {
    return $this->hasMany(Stock::class);
  }
}
