<?php

namespace App\Models\Movement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Auto\Location;
use App\Models\Movement\Malfunction;
use App\Models\Movement\Workers;
use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
  use  HasFactory;

  public $timestamps = false;

  protected $fillable = [
    'location_id',
    'date',
    'odometer',
  ];

  public function location()
  {
    return $this->belongsTo(Location::class, 'location_id', 'id');
  }

  public function malfunctions()
  {
    return $this->hasMany(Malfunction::class, 'id', 'id');
  }

  public function workers()
  {
    return $this->hasMany(Worker::class, 'id', 'id');
  }
}
