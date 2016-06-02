<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTvNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tv_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tvShow_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();
            $table->text('note');
            $table->timestamps();
            $table->unique(array('user_id', 'tvShow_id'));
            $table->foreign('tvShow_id')->references('moviedb_id')->on('tvShows')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tv_notes');
    }
}
