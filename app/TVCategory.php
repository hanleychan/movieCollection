<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TVCategory extends Model
{
	protected $table = 'tv_categories';
	protected $fillable = array('name');

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function tvCollections()
    {
    	return $this->hasMany(TVCollection::class, 'tv_category_id');
    }
}
