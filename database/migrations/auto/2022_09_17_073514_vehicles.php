<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
      Schema::create('vehicles', function (Blueprint $table) {
          $table->id();
          $table->unsignedBigInteger('version_id');
          $table->foreign('version_id')->references('id')->on('versions')->onDelete('cascade')->onUpdate('cascade');
      });
    }

    public function down()
    {
      Schema::dropIfExists('vehicles');
    }
};
