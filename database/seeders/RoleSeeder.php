<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Crear roles
        $admin = Role::create(['name' => 'ADMIN']);
        $vendedor = Role::create(['name' => 'VENDEDOR']);
        $jefeInstalador = Role::create(['name' => 'JEFE_INSTALADOR']);
        $disenador = Role::create(['name' => 'DISEÑADOR']);
        $instalador = Role::create(['name' => 'INSTALADOR']);

        // Crear permisos
        $permissions = [
            // CU1: Gestión de Usuarios
            'usuarios.index',
            'usuarios.create',
            'usuarios.edit',
            'usuarios.delete',
            
            // CU2: Gestión de Proyectos
            'proyectos.index',
            'proyectos.create',
            'proyectos.edit',
            'proyectos.delete',
            'cronogramas.manage',
            
            // CU3: Gestión de Productos
            'productos.index',
            'productos.create',
            'productos.edit',
            'productos.delete',
            
            // CU4: Gestión de Diseños
            'disenos.index',
            'disenos.create',
            'disenos.edit',
            'disenos.delete',
            
            // CU5: Gestión de Asignaciones
            'asignaciones.manage',
            
            // CU6: Gestión de Inventario
            'inventario.index',
            'inventario.ingreso',
            'inventario.salida',
            'proveedores.manage',
            
            // CU7: Gestión de Pagos
            'pagos.index',
            'pagos.create',
            'pagos.edit',
            'plan_pagos.manage',
            
            // CU8: Reportes y Estadísticas
            'reportes.index',
            'estadisticas.view',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Asignar permisos a roles
        
        // ADMIN: Todos los permisos
        $admin->givePermissionTo(Permission::all());

        // VENDEDOR: CU1, CU2, CU5, CU7
        $vendedor->givePermissionTo([
            'usuarios.index',
            'proyectos.index',
            'proyectos.create',
            'proyectos.edit',
            'cronogramas.manage',
            'asignaciones.manage',
            'pagos.index',
            'pagos.create',
            'plan_pagos.manage',
        ]);

        // JEFE_INSTALADOR: CU3, CU5, CU6
        $jefeInstalador->givePermissionTo([
            'productos.index',
            'productos.create',
            'productos.edit',
            'cronogramas.manage',
            'asignaciones.manage',
            'inventario.index',
            'inventario.ingreso',
            'inventario.salida',
            'proveedores.manage',
        ]);

        // DISEÑADOR: CU4
        $disenador->givePermissionTo([
            'disenos.index',
            'disenos.edit',
        ]);

        // INSTALADOR: CU3, CU6 (limitado)
        $instalador->givePermissionTo([
            'productos.index',
            'inventario.salida',
        ]);
    }
}
