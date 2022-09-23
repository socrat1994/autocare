<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    Schema::create('branches', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('location');
        $table->decimal('latitude',$precision = 11, $scale = 8)->nullable(true);
        $table->decimal('longitude',$precision = 11, $scale = 8)->nullable(true);
        $table->unsignedBigInteger('company_id');
        $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
    });
  }

  public function down()
  {
      Schema::dropIfExists('branches');
  }
};
