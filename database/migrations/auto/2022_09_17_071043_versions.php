<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
      Schema::create('versions', function (Blueprint $table) {
          $table->id();
          $table->unsignedBigInteger('model_id');
          $table->unsignedBigInteger('fuel_id');
          $table->integer('model_year');
          $table->foreign('model_id')->references('id')->on('models')->onDelete('cascade')->onUpdate('cascade');
          $table->foreign('fuel_id')->references('id')->on('fuels')->onDelete('cascade')->onUpdate('cascade');
      });
    }

    public function down()
    {
     Schema::dropIfExists('versions');
    }
};
