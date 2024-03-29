<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    Schema::create('models', function (Blueprint $table) {
        $table->id();
        $table->string('name')->unique();
        $table->string('url');
        $table->unsignedBigInteger('manufactuerer_id');
        $table->foreign('manufactuerer_id')->references('id')->on('manufactuerers')->onDelete('cascade')->onUpdate('cascade');
    });
  }

  public function down()
  {
      Schema::dropIfExists('manufactuerers');
  }
};
