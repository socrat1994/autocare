<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
  Schema::create('malfunctions', function (Blueprint $table) {
      $table->id();
      $table->string('reason');
      $table->foreign('id')->references('id')->on('locations')->onDelete('cascade')->onUpdate('cascade');
  });
}

public function down()
{
  Schema::dropIfExists('malfunctions');
}
};
