<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
  Schema::create('mal_comments', function (Blueprint $table) {
      $table->id();
      $table->text('comment');
      $table->date('date');
      $table->unsignedBigInteger('user_id');
      $table->unsignedBigInteger('malfunction_id');
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
      $table->foreign('malfunction_id')->references('id')->on('malfunctions')->onDelete('cascade')->onUpdate('cascade');
  });
}

public function down()
{
  Schema::dropIfExists('mal_comments');
}
};
