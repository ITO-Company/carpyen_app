<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuarios de ejemplo (uno por cada rol)
        
        // 1. Administrador
        $admin = User::create([
            'nombre' => 'Administrador',
            'email' => 'admin@carpyen.com',
            'password' => '12345',
            'telefono' => '70000000',
            'direccion' => 'Santa Cruz, Bolivia',
            'rol' => 'ADMIN',
        ]);

        // 2. Vendedor
        $vendedor = User::create([
            'nombre' => 'Carlos Vendedor',
            'email' => 'vendedor@carpyen.com',
            'password' => '12345',
            'telefono' => '70111111',
            'direccion' => 'Santa Cruz, Bolivia',
            'rol' => 'VENDEDOR',
        ]);

        // 3. Jefe Instalador
        $jefeInstalador = User::create([
            'nombre' => 'Juan Jefe Instalador',
            'email' => 'jefe@carpyen.com',
            'password' => '12345',
            'telefono' => '70222222',
            'direccion' => 'Santa Cruz, Bolivia',
            'rol' => 'JEFE_INSTALADOR',
        ]);

        // 4. Diseñador
        $disenador = User::create([
            'nombre' => 'María Diseñadora',
            'email' => 'disenador@carpyen.com',
            'password' => '12345',
            'telefono' => '70333333',
            'direccion' => 'Santa Cruz, Bolivia',
            'rol' => 'DISEÑADOR',
        ]);

        // 5. Instalador
        $instalador = User::create([
            'nombre' => 'Pedro Instalador',
            'email' => 'instalador@carpyen.com',
            'password' => '12345',
            'telefono' => '70444444',
            'direccion' => 'Santa Cruz, Bolivia',
            'rol' => 'INSTALADOR',
        ]);

        echo "✅ 5 usuarios creados exitosamente:\n";
        echo "   - admin@carpyen.com (ADMIN)\n";
        echo "   - vendedor@carpyen.com (VENDEDOR)\n";
        echo "   - jefe@carpyen.com (JEFE_INSTALADOR)\n";
        echo "   - disenador@carpyen.com (DISEÑADOR)\n";
        echo "   - instalador@carpyen.com (INSTALADOR)\n";
        echo "   Contraseña para todos: 12345\n";
    }
}
