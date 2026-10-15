<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(
            ['code' => 'admin'],
            ['name' => 'Administrador']
        );

        $empleadoRole = Role::firstOrCreate(
            ['code' => 'empleado'],
            ['name' => 'Empleado']
        );

        User::updateOrCreate(
            ['email' => 'pipelozada994@gmail.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('Admin123'),
                'role_id' => $adminRole->id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'pipelozada994@icloud.com'],
            [
                'name' => 'Empleado',
                'password' => Hash::make('Admin123'),
                'role_id' => $empleadoRole->id,
            ]
        );
    }
}
