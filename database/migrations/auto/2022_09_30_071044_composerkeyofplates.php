<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
      Schema::table('plates', function (Blueprint $table) {
          $table->unique(["vehicle_id", "vin_number", 'plate_number'], 'composerkeyplate');
      });
    }

    public function down()
    {
      Schema::table('plates', function (Blueprint $table) {
          $table->dropUnique('composerkeyplate');
        });
    }
};
