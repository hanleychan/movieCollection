<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovieNote extends Model
{

	protected $casts = [
		'movie_id' => 'integer',
	];

	protected $fillable = array('movie_id', 'note'); 
	
	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
