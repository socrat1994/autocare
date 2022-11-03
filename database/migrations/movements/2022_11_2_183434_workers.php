<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
  Schema::create('workers', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('driver_id');
      $table->unsignedBigInteger('assistant_id');
      $table->foreign('driver_id')->references('id')->on('employees')->onDelete('cascade')->onUpdate('cascade');
      $table->foreign('assistant_id')->references('id')->on('employees')->onDelete('cascade')->onUpdate('cascade');
  });
}

public function down()
{
  Schema::dropIfExists('workers');
}
};
