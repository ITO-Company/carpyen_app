<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Verificar si los roles existen, si no, crearlos
        $roles = ['ADMIN', 'VENDEDOR', 'JEFE_INSTALADOR', 'DISEÑADOR', 'INSTALADOR'];
        
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // Crear usuarios de ejemplo (uno por cada rol)
        
        // 1. Administrador
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@carpyen.com',
            'password' => bcrypt('12345'),
            'telefono' => '70000000',
            'direccion' => 'Santa Cruz, Bolivia',
            'rol' => 'ADMIN',
        ]);
        $admin->assignRole('ADMIN');

        // 2. Vendedor
        $vendedor = User::create([
            'name' => 'Carlos Vendedor',
            'email' => 'vendedor@carpyen.com',
            'password' => bcrypt('12345'),
            'telefono' => '70111111',
            'direccion' => 'Santa Cruz, Bolivia',
            'rol' => 'VENDEDOR',
        ]);
        $vendedor->assignRole('VENDEDOR');

        // 3. Jefe Instalador
        $jefeInstalador = User::create([
            'name' => 'Juan Jefe Instalador',
            'email' => 'jefe@carpyen.com',
            'password' => bcrypt('12345'),
            'telefono' => '70222222',
            'direccion' => 'Santa Cruz, Bolivia',
            'rol' => 'JEFE_INSTALADOR',
        ]);
        $jefeInstalador->assignRole('JEFE_INSTALADOR');

        // 4. Diseñador
        $disenador = User::create([
            'name' => 'María Diseñadora',
            'email' => 'disenador@carpyen.com',
            'password' => bcrypt('12345'),
            'telefono' => '70333333',
            'direccion' => 'Santa Cruz, Bolivia',
            'rol' => 'DISEÑADOR',
        ]);
        $disenador->assignRole('DISEÑADOR');

        // 5. Instalador
        $instalador = User::create([
            'name' => 'Pedro Instalador',
            'email' => 'instalador@carpyen.com',
            'password' => bcrypt('12345'),
            'telefono' => '70444444',
            'direccion' => 'Santa Cruz, Bolivia',
            'rol' => 'INSTALADOR',
        ]);
        $instalador->assignRole('INSTALADOR');

        echo "✅ 5 usuarios creados exitosamente:\n";
        echo "   - admin@carpyen.com (ADMIN)\n";
        echo "   - vendedor@carpyen.com (VENDEDOR)\n";
        echo "   - jefe@carpyen.com (JEFE_INSTALADOR)\n";
        echo "   - disenador@carpyen.com (DISEÑADOR)\n";
        echo "   - instalador@carpyen.com (INSTALADOR)\n";
        echo "   Contraseña para todos: 12345\n";
    }
}
