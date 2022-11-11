<?php

namespace App\Models\Movement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Movement\Malcomment;
use App\Models\Movement\Movement;
use Illuminate\Database\Eloquent\Model;

class Malfunction extends Model
{
  use  HasFactory;

  public $timestamps = false;

  protected $fillable = [
    'id',
    'reason',
  ];

  public function Movement()
  {
    return $this->belongsTo(Movement::class, 'id', 'id');
  }

  public function comments()
  {
    return $this->hasMany(Malcomment::class, 'malfunction_id', 'id');
  }
}
