<?php

namespace App\Models\Auto;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Auto\Plate;
use App\Models\Branch;
use App\Models\Movement\Movement;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
  use  HasFactory;

  public $timestamps = false;

  protected $fillable = [
    'car_number',
    'moved_at',
    'branch_id',
    'plate_id'
  ];

  public function vehicle_plate()
  {
    return $this->belongsTo(Plate::class, 'plate_id', 'id');
  }

  public function b_vehicle_plate()
  {
    return $this->belongsTo(Branch::class, 'branch_id', 'id');
  }

  public function movements()
  {
    return $this->hasMany(Movement::class, 'location_id', 'id');
  }  
}
