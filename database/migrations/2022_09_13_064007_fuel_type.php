<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    Schema::create('fuel_type', function (Blueprint $table) {
        $table->id();
        $table->string('type')->unique();
    });
  }

  public function down()
  {
      Schema::dropIfExists('fuel_type');
  }
};
