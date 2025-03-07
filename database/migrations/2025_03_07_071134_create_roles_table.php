<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });
        
        // Roles tablosuna varsayılan rolleri ekleyelim
        DB::table('roles')->insert([
            ['name' => 'user', 'description' => 'Normal kullanıcı', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'admin', 'description' => 'Yönetici', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'super_admin', 'description' => 'Süper Yönetici', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('roles');
    }
}