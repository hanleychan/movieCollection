<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function movieCategories()
    {
        return $this->hasMany(MovieCategory::class);
    }
    public function tvCategories()
    {
        return $this->hasMany(TVCategory::class);
    }

    public function movieNotes()
    {
        return $this->hasMany(MovieNote::class);
    }

    public function tvNotes()
    {
        return $this->hasMany(TVNote::class);
    }
}
