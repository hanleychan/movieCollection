<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TVShow extends Model
{
	protected $table = 'tvShows';
	protected $fillable = array('title', 'release', 'moviedb_id', 'poster');
	protected $primaryKey = 'moviedb_id';

	public function tvCollections()
	{
		return $this->hasMany(TVCollection::class);
	}

	public function tvNotes()
	{
		return $this->hasMany(TVNotes::class);
	}
}
