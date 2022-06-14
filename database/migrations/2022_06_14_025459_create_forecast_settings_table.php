<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('forecast_settings', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('stock_id');
      $table->integer('default_kg_per_day');
      $table->integer('reordering_point');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('forecast_settings');
  }
};
