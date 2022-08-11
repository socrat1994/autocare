<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{public function up()
{
  Schema::create('employees', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('user_id');
      $table->unsignedBigInteger('branch_id');
      $table->date('moved_at');
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
      $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade')->onUpdate('cascade');
  });
}

public function down()
{
    Schema::dropIfExists('employees');
}
};
