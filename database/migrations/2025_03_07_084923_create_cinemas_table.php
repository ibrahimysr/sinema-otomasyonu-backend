<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCinemasTable extends Migration
{
    public function up()
    {
        Schema::create('cinemas', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedTinyInteger('city_id'); 
            $table->string('name'); 
            $table->text('address'); 
            $table->decimal('latitude', 10, 7)->nullable(); 
            $table->decimal('longitude', 10, 7)->nullable(); 
            $table->integer('total_capacity')->default(0); 
            $table->string('phone')->nullable(); 
            $table->text('description')->nullable(); 
            $table->timestamps(); 

            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cinemas');
    }
}