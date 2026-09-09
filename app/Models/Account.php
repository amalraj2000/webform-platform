<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function forms()
    {
        return $this->hasMany(Form::class);
    }
}
