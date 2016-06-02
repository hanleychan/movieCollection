<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TVNote extends Model
{
	protected $table = 'tv_notes';
	protected $fillable = array('tvShow_id', 'note'); 
	protected $casts = [
		'tvShow_id' => 'integer',
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function tvShow()
	{
		return $this->belongsTo(TVShow::class);
	}
}
