<?php

namespace App\Models\Auto;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Auto\Plate;
use App\Models\Auto\Version;

class Vehicle extends Model
{
  use  HasFactory;

  public $timestamps = false;

  protected $fillable = [
    'version_id'
  ];

  public function plates()
  {
    return $this->hasMany(Plate::class, 'vehicle_id', 'id');
  }
  public function version()
  {
    return $this->belongsTo(Version::class, 'version_id', 'id');
  }
}
