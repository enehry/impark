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
    Schema::create('issue_products', function (Blueprint $table) {
      $table->id();
      $table->integer('total_price');
      $table->integer('sold_quantity');
      $table->unsignedBigInteger('stock_id');
      $table->string('issue_id');
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
    Schema::dropIfExists('issue_products');
  }
};
