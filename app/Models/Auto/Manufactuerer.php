<?php

namespace App\Models\Auto;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Auto\CarModel;
use Illuminate\Database\Eloquent\Model;


class Manufactuerer extends Model
{
  use  HasFactory;

  public $timestamps = false;

  protected $fillable = [
    'name',
    'url'
  ];

  public function models()
  {
    return $this->hasMany(CarModel::class, 'manufactuerer_id', 'id');
  }
}
