<?php

namespace App\Models\Auto;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Auto\Location;
use App\Models\Auto\Vehicle;

class Plate extends Model
{
  use  HasFactory;

  public $timestamps = false;

  protected $fillable = [
    'vin_number',
    'plate_number',
    'changed_at',
    'vehicle_id'
  ];

  public function locations()
  {
    return $this->hasMany(Location::class, 'plate_id', 'id');
  }

  public function vehicle()
  {
    return $this->belongsTo(Vehicle::class, 'vehicle_id', 'id');
  }
}
