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
    Schema::create('stocks', function (Blueprint $table) {
      $table->id();
      $table->integer('quantity');
      $table->boolean('is_forecasted')->default(false);
      $table->unsignedBigInteger('product_id');
      $table->unsignedBigInteger('branch_id');
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
    Schema::dropIfExists('stocks');
  }
};
