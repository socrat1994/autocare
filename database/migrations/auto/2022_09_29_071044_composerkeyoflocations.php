<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
      Schema::table('locations', function (Blueprint $table) {
          $table->unique(["car_number", "branch_id", "moved_at", 'plate_id'], 'composerkeylocation');
      });
    }

    public function down()
    {
      Schema::table('locations', function (Blueprint $table) {
          $table->dropUnique('composerkeylocation');
        });
    }
};
