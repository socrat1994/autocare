<?php

namespace App\Models\Movement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Movement\Movement;
use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
  use  HasFactory;

  public $timestamps = false;

  protected $fillable = [
    'id',
    'driver_id',
    'assistant_id',
  ];

  public function Movement()
  {
    return $this->belongsTo(Movement::class, 'id', 'id');
  }
}
