<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Company extends Authenticatable
{
    use  HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'active'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'owner', 'id');
    }

    public function branch()
       {
           return $this->hasMany(Branch::class, 'company_id', 'id');
       }
}
