<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TVCollection extends Model
{
	protected $table = 'tv_collections';
	protected $fillable = array('tvShow_id');
	protected $casts = [
		'tvShow_id' => 'integer',
	];

	public function tvCategory()
	{
		return $this->belongsTo(TVCategory::class);
	}

	public function tvShow()
	{
		return $this->belongsTo(TVShow::class, 'tvShow_id');
	}
}
