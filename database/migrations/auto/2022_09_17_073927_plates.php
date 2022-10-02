<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
      Schema::create('plates', function (Blueprint $table) {
          $table->id();
          $table->string('vin_number');
          $table->string('plate_number');
          $table->date('changed_at');
          $table->unsignedBigInteger('vehicle_id');
          $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade')->onUpdate('cascade');
      });
    }

    public function down()
    {
      Schema::dropIfExists('plates');
    }
};
