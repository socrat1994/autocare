<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
      Schema::table('versions', function (Blueprint $table) {
          $table->unique(["model_id", "fuel_id", 'model_year'], 'composerkeyversion');
      });
    }

    public function down()
    {
      Schema::table('versions', function (Blueprint $table) {
          $table->dropUnique('composerkeyversion');
        });
    }
};
