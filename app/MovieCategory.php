<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovieCategory extends Model
{
	protected $fillable = array('name');

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function movieCollections()
    {
    	return $this->hasMany(MovieCollection::class);
    }
}
