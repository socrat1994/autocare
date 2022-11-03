<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
  Schema::create('movements', function (Blueprint $table) {
      $table->id();
      $table->string('car_number');
      $table->date('date');
      $table->unsignedBigInteger('location_id');
      $table->integer('odometer');
      $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade')->onUpdate('cascade');
  });
}

public function down()
{
  Schema::dropIfExists('movements');
}
};
