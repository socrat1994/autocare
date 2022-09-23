<?php

namespace App\Models\Auto;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Auto\Type;
use App\Models\Auto\Manufactuerer;
use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
  use  HasFactory;

  public $timestamps = false;
  protected $table = 'models';
  protected $fillable = [
    'name',
    'url',
    'manufactuerer_id'
  ];

  public function vehicles()
  {
    return $this->hasMany(Type::class, 'model_id', 'id');
  }

  public function Manufactuerer()
  {
    return $this->belongsTo(Manufactuerer::class, 'manufactuerer_id', 'id');
  }
}
