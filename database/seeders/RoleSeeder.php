<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->upsert(
            [
                ['code' => 'admin', 'name' => 'Administrador'],
                ['code' => 'empleado', 'name' => 'Empleado'],
            ],
            ['code'],
            ['name']
        );
    }
}
