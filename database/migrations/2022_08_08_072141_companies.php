<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
      Schema::create('companies', function (Blueprint $table) {
          $table->id();
          $table->string('name')->unique();
          $table->unsignedBigInteger('owner');
          $table->boolean('active');
          $table->foreign('owner')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
      });
    }

    public function down()
    {
        Schema::dropIfExists('companies');
    }
};
