<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->string('email')->unique(); 
            $table->string('password'); 
            $table->string('api_token', 80)->unique()->nullable(); 
            $table->unsignedBigInteger('role_id')->default(1); 
            
            $table->timestamps(); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');

    }
}
