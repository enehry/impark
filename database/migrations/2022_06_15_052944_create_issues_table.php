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
    Schema::create('issues', function (Blueprint $table) {
      $table->string('id')->primary();
      $table->unsignedBigInteger('user_id');
      $table->unsignedBigInteger('branch_id');
      $table->decimal('sum_total_price');
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
    Schema::dropIfExists('issues');
  }
};
