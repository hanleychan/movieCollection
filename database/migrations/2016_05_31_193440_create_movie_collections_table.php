<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMovieCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movie_collections', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('movie_id')->unsigned()->index();
            $table->integer('movie_category_id')->unsigned()->index();
            $table->timestamps();
            $table->unique(array('movie_id', 'movie_category_id'));
            $table->foreign('movie_id')->references('moviedb_id')->on('movies')->onDelete('cascade');
            $table->foreign('movie_category_id')->references('id')->on('movie_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('movie_collections');
    }
}
