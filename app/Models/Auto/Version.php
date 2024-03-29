<?php

namespace App\Models\Auto;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Auto\Vehicle;
use App\Models\Auto\CarModel;
use Illuminate\Database\Eloquent\Model;


class Version extends Model
{
  use  HasFactory;

  public $timestamps = false;

  protected $fillable = [
    'model_id',
    'fuel_id',
    'model_year'
  ];

  public function vehicles()
  {
    return $this->hasMany(Vehicle::class, 'version_id', 'id');
  }

  public function model()
  {
    return $this->belongsTo(CarModel::class, 'model_id', 'id');
  }
}
