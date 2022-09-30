<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
  Schema::create('locations', function (Blueprint $table) {
      $table->id();
      $table->string('car_number');
      $table->date('moved_at');
      $table->unsignedBigInteger('branch_id');
      $table->unsignedBigInteger('plate_id');
      $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade')->onUpdate('cascade');
      $table->foreign('plate_id')->references('id')->on('plates')->onDelete('cascade')->onUpdate('cascade');
  });
}

public function down()
{
  Schema::dropIfExists('locations');
}
};
