<?php

namespace App\Models\Movement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Movement\Malfunction;
use Illuminate\Database\Eloquent\Model;

class Malcomment extends Model
{
  use  HasFactory;

  public $timestamps = false;
  protected $table = 'mal_comments';
  protected $fillable = [
    'comment',
    'date',
    'user_id',
    'malfunction_id',
  ];

  public function malfunction()
  {
    return $this->belongsTo(Malfunction::class, 'malfunction_id', 'id');
  }
}
