<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Cliente;
use App\Models\Proyecto;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Cotizacion;
use App\Models\Diseno;
use App\Models\Cronograma;
use App\Models\Tarea;
use App\Models\PlanPago;
use App\Models\Pago;
use App\Models\User;

class DataSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar caches de PostgreSQL antes de seedear
        try {
            DB::unprepared('DEALLOCATE ALL;');
        } catch (\Exception $e) {
            // Ignorar
        }
        
        try {
            DB::unprepared('DISCARD PLANS;');
        } catch (\Exception $e) {
            // Ignorar
        }

        // Crear Clientes
        $clientes = [
            [
                'nombre' => 'María González',
                'email' => 'maria.gonzalez@email.com',
                'telefono' => '70111222',
                'direccion' => 'Av. Cristo Redentor #123, Santa Cruz',
                'password' => Hash::make('12345')
            ],
            [
                'nombre' => 'Carlos Rodríguez',
                'email' => 'carlos.rodriguez@email.com',
                'telefono' => '70222333',
                'direccion' => 'Calle Libertad #456, Santa Cruz',
                'password' => Hash::make('12345')
            ],
            [
                'nombre' => 'Ana Martínez',
                'email' => 'ana.martinez@email.com',
                'telefono' => '70333444',
                'direccion' => 'Av. Banzer #789, Santa Cruz',
                'password' => Hash::make('12345')
            ],
            [
                'nombre' => 'Luis Fernández',
                'email' => 'luis.fernandez@email.com',
                'telefono' => '70444555',
                'direccion' => 'Barrio Equipetrol, Santa Cruz',
                'password' => Hash::make('12345')
            ],
            [
                'nombre' => 'Patricia López',
                'email' => 'patricia.lopez@email.com',
                'telefono' => '70555666',
                'direccion' => 'Av. Roca y Coronado #321, Santa Cruz',
                'password' => Hash::make('12345')
            ]
        ];

        foreach ($clientes as $clienteData) {
            Cliente::create($clienteData);
        }

        // Crear Productos
        $productos = [
            ['nombre' => 'Madera de Roble', 'tipo' => 'Materia Prima', 'unidad_medida' => 'metro', 'precio_unitario' => 150.00, 'stock' => 50],
            ['nombre' => 'Madera de Cedro', 'tipo' => 'Materia Prima', 'unidad_medida' => 'metro', 'precio_unitario' => 180.00, 'stock' => 40],
            ['nombre' => 'Bisagras Premium', 'tipo' => 'Herraje', 'unidad_medida' => 'unidad', 'precio_unitario' => 25.00, 'stock' => 200],
            ['nombre' => 'Tornillos 3"', 'tipo' => 'Herraje', 'unidad_medida' => 'caja', 'precio_unitario' => 15.00, 'stock' => 100],
            ['nombre' => 'Barniz Transparente', 'tipo' => 'Acabado', 'unidad_medida' => 'litro', 'precio_unitario' => 80.00, 'stock' => 30],
            ['nombre' => 'Laca Brillante', 'tipo' => 'Acabado', 'unidad_medida' => 'litro', 'precio_unitario' => 95.00, 'stock' => 25],
            ['nombre' => 'Vidrio Templado', 'tipo' => 'Material', 'unidad_medida' => 'metro_cuadrado', 'precio_unitario' => 120.00, 'stock' => 15],
            ['nombre' => 'Manijas Modernas', 'tipo' => 'Herraje', 'unidad_medida' => 'unidad', 'precio_unitario' => 45.00, 'stock' => 80],
        ];

        foreach ($productos as $productoData) {
            Producto::create($productoData);
        }

        // Crear Proveedores
        $proveedores = [
            ['nombre' => 'Maderas del Norte', 'contacto' => 'Juan Pérez', 'telefono' => '3-3456789', 'email' => 'info@maderarodelnorte.com', 'ubicacion' => 'Zona Norte, Santa Cruz'],
            ['nombre' => 'Herrajes Industriales', 'contacto' => 'Pedro Sánchez', 'telefono' => '3-3567890', 'email' => 'contacto@herrajesindustriales.com', 'ubicacion' => 'Parque Industrial, Santa Cruz'],
            ['nombre' => 'Pinturas y Acabados', 'contacto' => 'Laura Gómez', 'telefono' => '3-3678901', 'email' => 'ventas@pinturasacabados.com', 'ubicacion' => 'Av. Santos Dumont, Santa Cruz'],
        ];

        foreach ($proveedores as $proveedorData) {
            Proveedor::create($proveedorData);
        }

        // Obtener usuarios por rol
        $vendedor = User::where('rol', 'VENDEDOR')->first();
        $disenador = User::where('rol', 'DISEÑADOR')->first();
        $instalador = User::where('rol', 'INSTALADOR')->first();

        // Crear Proyectos
        $proyectos = [
            [
                'nombre' => 'Cocina Integral Moderna',
                'descripcion' => 'Diseño y fabricación de cocina integral con acabados en madera de roble',
                'ubicacion' => 'Equipetrol, Santa Cruz',
                'estado' => 'en_proceso',
                'cliente_id' => 1,
                'user_id' => $vendedor ? $vendedor->id : null
            ],
            [
                'nombre' => 'Closet Principal',
                'descripcion' => 'Closet de 3 puertas con cajones y zapatero',
                'ubicacion' => 'Barrio Las Palmas, Santa Cruz',
                'estado' => 'pendiente',
                'cliente_id' => 2,
                'user_id' => $vendedor ? $vendedor->id : null
            ],
            [
                'nombre' => 'Muebles de Oficina',
                'descripcion' => 'Escritorio ejecutivo y estantería',
                'ubicacion' => 'Centro, Santa Cruz',
                'estado' => 'en_proceso',
                'cliente_id' => 3,
                'user_id' => $vendedor ? $vendedor->id : null
            ],
            [
                'nombre' => 'Mueble de TV',
                'descripcion' => 'Mueble modular para sala de estar',
                'ubicacion' => 'Urbarí, Santa Cruz',
                'estado' => 'completado',
                'cliente_id' => 4,
                'user_id' => $vendedor ? $vendedor->id : null
            ]
        ];

        foreach ($proyectos as $proyectoData) {
            $proyecto = Proyecto::create($proyectoData);

            // Crear Cotización para cada proyecto
            $cotizacion = Cotizacion::create([
                'proyecto_id' => $proyecto->id,
                'tipo_metro' => 'cuadrado',
                'costo_metro' => 150.00,
                'cantidad_metro' => 10,
                'costo_mueble' => 3000.00,
                'mueble_numero' => 2,
                'total' => 8000.00,
                'estado' => 'aprobada',
                'comentario' => 'Cotización para ' . $proyecto->nombre
            ]);

            // Crear Diseño con imágenes reales
            if ($disenador) {
                $imagenesDiseno = [
                    'https://planner5d.com/blog/content/images/2023/02/r-architecture-rOk4VSMS3Ck-unsplash.jpg',
                    'https://media1.amarilo.com.co/website/s3fs-public/2023-12/disen%CC%83o-de-interiores.webp',
                    'https://i.pinimg.com/474x/63/4e/d6/634ed6c0b74885239969c26478654bda.jpg',
                    'https://cei.es/wp-content/uploads/estilos-en-el-disen%CC%83o-de-interiores-scaled.jpg'
                ];
                
                $descripcionesDiseno = [
                    'Diseño moderno con acabados en madera natural y tonos cálidos. Incluye iluminación LED integrada.',
                    'Estilo contemporáneo con líneas minimalistas. Combinación de materiales nobles y funcionalidad.',
                    'Diseño elegante con detalles personalizados. Aprovechamiento máximo del espacio disponible.',
                    'Propuesta moderna con enfoque en ergonomía. Acabados premium y atención al detalle.'
                ];
                
                $indexImagen = ($proyecto->id - 1) % count($imagenesDiseno);
                $indexDescripcion = ($proyecto->id - 1) % count($descripcionesDiseno);
                
                Diseno::create([
                    'cotizacion_id' => $cotizacion->id,
                    'proyecto_id' => $proyecto->id,
                    'user_id' => $disenador->id,
                    'url_render' => $imagenesDiseno[$indexImagen],
                    'plano_iluminador' => '/planos/proyecto_' . $proyecto->id . '.pdf',
                    'aprovado' => $proyecto->estado !== 'pendiente',
                    'fecha_aprovacion' => $proyecto->estado !== 'pendiente' ? now()->subDays(7) : null,
                    'comentario' => 'Diseño 3D para ' . $proyecto->nombre,
                    'descripcion' => $descripcionesDiseno[$indexDescripcion],
                    'estado' => $proyecto->estado === 'completado' ? 'completado' : ($proyecto->estado === 'en_proceso' ? 'en_proceso' : 'pendiente'),
                    'fecha_inicio' => $proyecto->estado !== 'pendiente' ? now()->subDays(20) : null,
                    'fecha_fin' => $proyecto->estado === 'completado' ? now()->subDays(5) : null
                ]);
            }

            // Crear Plan de Pago
            $planPago = PlanPago::create([
                'proyecto_id' => $proyecto->id,
                'deuda_total' => 8000.00,
                'pagado_total' => $proyecto->estado === 'completado' ? 8000.00 : 4000.00,
                'numero_deudas' => 2,
                'numero_pagos' => $proyecto->estado === 'completado' ? 2 : 1,
                'estado' => $proyecto->estado === 'completado' ? 'completado' : 'activo'
            ]);

            // Crear Pagos
            Pago::create([
                'plan_pago_id' => $planPago->id,
                'fecha' => now()->subDays(10),
                'total' => 4000.00,
                'metodo_pago' => 'transferencia',
                'estado' => 'completado'
            ]);

            if ($proyecto->estado === 'completado') {
                Pago::create([
                    'plan_pago_id' => $planPago->id,
                    'fecha' => now()->subDays(2),
                    'total' => 4000.00,
                    'metodo_pago' => 'efectivo',
                    'estado' => 'completado'
                ]);
            }

            // Crear Cronograma
            if ($proyecto->estado !== 'pendiente') {
                $cronograma = Cronograma::create([
                    'proyecto_id' => $proyecto->id,
                    'usuario_id' => $instalador ? $instalador->id : 1,
                    'fecha_inicio' => now()->subDays(15),
                    'fecha_fin' => now()->addDays(15),
                    'dias_estimados' => 30,
                    'estado' => $proyecto->estado === 'completado' ? 'completado' : 'en_curso'
                ]);

                Tarea::create([
                    'cronograma_id' => $cronograma->id,
                    'user_id' => $instalador ? $instalador->id : null,
                    'fecha' => now()->subDays(15),
                    'descripcion' => 'Corte de materiales',
                    'hora_inicio' => '08:00',
                    'hora_fin' => '12:00',
                    'estado' => 'completada'
                ]);

                Tarea::create([
                    'cronograma_id' => $cronograma->id,
                    'user_id' => $instalador ? $instalador->id : null,
                    'fecha' => now()->subDays(14),
                    'descripcion' => 'Ensamblaje',
                    'hora_inicio' => '13:00',
                    'hora_fin' => '17:00',
                    'estado' => $proyecto->estado === 'completado' ? 'completada' : 'en_proceso'
                ]);

                if ($proyecto->estado !== 'completado') {
                    Tarea::create([
                        'cronograma_id' => $cronograma->id,
                        'user_id' => $instalador ? $instalador->id : null,
                        'fecha' => now()->addDays(1),
                        'descripcion' => 'Acabados finales',
                        'hora_inicio' => '08:00',
                        'hora_fin' => '12:00',
                        'estado' => 'pendiente'
                    ]);
                }
            }
        }

        echo "✅ Datos de ejemplo creados exitosamente:\n";
        echo "   - " . count($clientes) . " Clientes\n";
        echo "   - " . count($productos) . " Productos\n";
        echo "   - " . count($proveedores) . " Proveedores\n";
        echo "   - " . count($proyectos) . " Proyectos\n";
        echo "   - Con cotizaciones, diseños, cronogramas, tareas, planes de pago y pagos\n";
    }
}
