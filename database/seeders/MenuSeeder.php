<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // Dashboard
        Menu::create([
            'nombre' => 'Dashboard',
            'icono' => 'home',
            'ruta' => '/dashboard',
            'orden' => 1,
            'roles' => ['ADMIN', 'VENDEDOR', 'JEFE_INSTALADOR', 'DISEÑADOR', 'INSTALADOR'],
            'activo' => true,
        ]);

        // Gestión de Usuarios (CU1)
        $usuarios = Menu::create([
            'nombre' => 'Usuarios',
            'icono' => 'users',
            'ruta' => null,
            'orden' => 2,
            'roles' => ['ADMIN', 'VENDEDOR'],
            'activo' => true,
        ]);

        Menu::create([
            'nombre' => 'Lista de Usuarios',
            'icono' => 'list',
            'ruta' => '/usuarios',
            'orden' => 1,
            'parent_id' => $usuarios->id,
            'roles' => ['ADMIN', 'VENDEDOR'],
            'activo' => true,
        ]);

        // Gestión de Clientes y Proyectos (CU2)
        $proyectos = Menu::create([
            'nombre' => 'Proyectos',
            'icono' => 'briefcase',
            'ruta' => null,
            'orden' => 3,
            'roles' => ['ADMIN', 'VENDEDOR'],
            'activo' => true,
        ]);

        Menu::create([
            'nombre' => 'Clientes',
            'icono' => 'user-check',
            'ruta' => '/clientes',
            'orden' => 1,
            'parent_id' => $proyectos->id,
            'roles' => ['ADMIN', 'VENDEDOR'],
            'activo' => true,
        ]);

        Menu::create([
            'nombre' => 'Proyectos',
            'icono' => 'folder',
            'ruta' => '/proyectos',
            'orden' => 2,
            'parent_id' => $proyectos->id,
            'roles' => ['ADMIN', 'VENDEDOR'],
            'activo' => true,
        ]);

        Menu::create([
            'nombre' => 'Cotizaciones',
            'icono' => 'file-text',
            'ruta' => '/cotizaciones',
            'orden' => 3,
            'parent_id' => $proyectos->id,
            'roles' => ['ADMIN', 'VENDEDOR'],
            'activo' => true,
        ]);

        // Gestión de Productos (CU3)
        $productos = Menu::create([
            'nombre' => 'Cotizaciones',
            'icono' => 'package',
            'ruta' => null,
            'orden' => 4,
            'roles' => ['ADMIN', 'JEFE_INSTALADOR', 'INSTALADOR'],
            'activo' => true,
        ]);

        Menu::create([
            'nombre' => 'Productos',
            'icono' => 'box',
            'ruta' => '/productos',
            'orden' => 1,
            'parent_id' => $productos->id,
            'roles' => ['ADMIN', 'JEFE_INSTALADOR', 'INSTALADOR'],
            'activo' => true,
        ]);

        Menu::create([
            'nombre' => 'Proveedores',
            'icono' => 'truck',
            'ruta' => '/proveedores',
            'orden' => 2,
            'parent_id' => $productos->id,
            'roles' => ['ADMIN', 'JEFE_INSTALADOR'],
            'activo' => true,
        ]);

        Menu::create([
            'nombre' => 'Movimientos',
            'icono' => 'activity',
            'ruta' => '/inventario/movimientos',
            'orden' => 3,
            'parent_id' => $productos->id,
            'roles' => ['ADMIN', 'JEFE_INSTALADOR'],
            'activo' => true,
        ]);

        // Gestión de Diseños (CU4)
        Menu::create([
            'nombre' => 'Diseños',
            'icono' => 'layout',
            'ruta' => '/disenos',
            'orden' => 5,
            'roles' => ['ADMIN', 'DISEÑADOR', 'VENDEDOR'],
            'activo' => true,
        ]);

        // Gestión de Cronogramas y Tareas (CU5)
        $cronogramas = Menu::create([
            'nombre' => 'Cronogramas',
            'icono' => 'calendar',
            'ruta' => null,
            'orden' => 6,
            'roles' => ['ADMIN', 'JEFE_INSTALADOR', 'INSTALADOR'],
            'activo' => true,
        ]);

        Menu::create([
            'nombre' => 'Cronogramas',
            'icono' => 'calendar',
            'ruta' => '/cronogramas',
            'orden' => 1,
            'parent_id' => $cronogramas->id,
            'roles' => ['ADMIN', 'JEFE_INSTALADOR'],
            'activo' => true,
        ]);

        Menu::create([
            'nombre' => 'Mis Tareas',
            'icono' => 'check-square',
            'ruta' => '/tareas',
            'orden' => 2,
            'parent_id' => $cronogramas->id,
            'roles' => ['ADMIN', 'JEFE_INSTALADOR', 'INSTALADOR'],
            'activo' => true,
        ]);

        // Gestión de Pagos (CU7)
        $pagos = Menu::create([
            'nombre' => 'Pagos',
            'icono' => 'dollar-sign',
            'ruta' => null,
            'orden' => 7,
            'roles' => ['ADMIN', 'VENDEDOR'],
            'activo' => true,
        ]);

        Menu::create([
            'nombre' => 'Planes de Pago',
            'icono' => 'credit-card',
            'ruta' => '/plan-pagos',
            'orden' => 1,
            'parent_id' => $pagos->id,
            'roles' => ['ADMIN', 'VENDEDOR'],
            'activo' => true,
        ]);

        Menu::create([
            'nombre' => 'Registro de Pagos',
            'icono' => 'list',
            'ruta' => '/pagos',
            'orden' => 2,
            'parent_id' => $pagos->id,
            'roles' => ['ADMIN', 'VENDEDOR'],
            'activo' => true,
        ]);

        // Reportes y Estadísticas (CU8)
        $reportes = Menu::create([
            'nombre' => 'Reportes',
            'icono' => 'bar-chart',
            'ruta' => null,
            'orden' => 8,
            'roles' => ['ADMIN'],
            'activo' => true,
        ]);

        Menu::create([
            'nombre' => 'Estadísticas',
            'icono' => 'pie-chart',
            'ruta' => '/reportes/estadisticas',
            'orden' => 1,
            'parent_id' => $reportes->id,
            'roles' => ['ADMIN'],
            'activo' => true,
        ]);

        Menu::create([
            'nombre' => 'Reportes de Ventas',
            'icono' => 'trending-up',
            'ruta' => '/reportes/ventas',
            'orden' => 2,
            'parent_id' => $reportes->id,
            'roles' => ['ADMIN'],
            'activo' => true,
        ]);

        Menu::create([
            'nombre' => 'Reportes de Inventario',
            'icono' => 'package',
            'ruta' => '/reportes/inventario',
            'orden' => 3,
            'parent_id' => $reportes->id,
            'roles' => ['ADMIN'],
            'activo' => true,
        ]);
    }
}
