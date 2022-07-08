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
    Schema::create('planned_orders', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('stock_id');
      $table->unsignedBigInteger('user_id');
      $table->unsignedBigInteger('branch_id');
      $table->integer('order_quantity');
      // $table->integer('received_quantity')->default(0);
      $table->timestamp('issued_at')->nullable();
      $table->timestamp('delivered_at')->nullable();
      $table->timestamp('received_at')->nullable();
      $table->softDeletes();
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
    Schema::dropIfExists('planned_orders');
  }
};
