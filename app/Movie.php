<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
	protected $fillable = array('title', 'release', 'moviedb_id');
	protected $primaryKey = 'moviedb_id';

	public function movieCollections()
	{
		return $this->hasMany(MovieCollection::class);
	}

	public function movieNotes()
	{
		return $this->hasMany(MovieNote::class);
	}
}
