<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoviesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('genre')->nullable();
            $table->integer('duration')->nullable()->comment('Film süresi (dakika)');
            $table->string('poster_url')->nullable();
            $table->string('language')->nullable();
            $table->date('release_date')->nullable();
            $table->boolean('is_in_theaters')->default(true);
            $table->string('imdb_id')->nullable()->unique();
            $table->float('imdb_rating', 3, 1)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movies');
    }
} 