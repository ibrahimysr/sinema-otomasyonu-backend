<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            [
                'name' => 'user',
                'description' => 'Normal kullanıcı',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'admin',
                'description' => 'Yönetici',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'super_admin',
                'description' => 'Süper Yönetici',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}