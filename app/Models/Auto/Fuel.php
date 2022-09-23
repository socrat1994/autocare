<?php

namespace App\Models\Auto;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Auto\Type;
use Illuminate\Database\Eloquent\Model;

class Fuel extends Model
{
    use  HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'type',
    ];

    public function vehicles()
        {
           return $this->hasMany(Type::class, 'fuel_id', 'id');
        }
}
