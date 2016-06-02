<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovieCollection extends Model
{
	protected $casts = [
		'movie_id' => 'integer',
	];

	protected $fillable = array('movie_id');

	public function movieCategory()
	{
		return $this->belongsTo(MovieCategory::class);
	}

	public function movie()
	{
		return $this->belongsTo(Movie::class);
	}
}
