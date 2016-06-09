<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTvCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tv_collections', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tvShow_id')->unsigned()->index();
            $table->integer('tv_category_id')->unsigned()->index();
            $table->timestamps();
            $table->unique(array('tvShow_id', 'tv_category_id'));
            $table->foreign('tvShow_id')->references('moviedb_id')->on('tvShows')->onDelete('cascade');
            $table->foreign('tv_category_id')->references('id')->on('tv_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tv_collections');
    }
}
