<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{

    public function run()
    {
      $types = [
        'Diesel',
        'gasoline',
        'gas',
        'Electric',
      ];

      foreach($types as $type)
      {
        $exist = DB::table('fuel_type')->where('type','=', $type)->get();
        if($exist->isEmpty())
        {
          DB::table('fuel_type')->insert(['type' => $type]);
        }
      }
    }
}
