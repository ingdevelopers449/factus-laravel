<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->upsert(
            [
                ['code' => 'admin', 'name' => 'Administrador', 'nombre_rol' => 'Administrador'],
                ['code' => 'empleado', 'name' => 'Empleado', 'nombre_rol' => 'Empleado'],
            ],
            ['code'],
            ['name', 'nombre_rol']
        );
    }
}
