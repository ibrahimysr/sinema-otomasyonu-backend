<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cinema_hall_id');
            $table->json('seat_data')->comment('Koltuk düzeni (statik)');
            $table->string('status')->default('active')->comment('Genel durum: active, inactive, maintenance');
            $table->timestamps();
            
            $table->foreign('cinema_hall_id')
                  ->references('id')
                  ->on('cinema_halls')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seats');
    }
} 