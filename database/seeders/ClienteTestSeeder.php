<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;
use App\Models\Proyecto;
use App\Models\PlanPago;
use App\Models\Pago;

class ClienteTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test client with password "password123"
        $cliente = Cliente::firstOrCreate(
            ['email' => 'cliente@test.com'],
            [
                'nombre' => 'Juan Pérez',
                'telefono' => '70123456',
                'direccion' => 'Av. Banzer #123',
                'password' => 'password123', // Will be hashed automatically
            ]
        );

        // Create a test project for this client
        $proyecto = Proyecto::firstOrCreate(
            [
                'nombre' => 'Cocina Moderna',
                'cliente_id' => $cliente->id,
            ],
            [
                'descripcion' => 'Instalación de cocina integral con muebles de madera',
                'ubicacion' => 'Santa Cruz, Bolivia',
                'estado' => 'en_proceso',
            ]
        );

        // Create a payment plan for this project
        $planPago = PlanPago::firstOrCreate(
            ['proyecto_id' => $proyecto->id],
            [
                'deuda_total' => 15000.00,
                'pagado_total' => 5000.00,
                'numero_deudas' => 3,
                'numero_pagos' => 1,
                'estado' => 'activo',
            ]
        );

        // Create some payments for this plan
        Pago::firstOrCreate(
            [
                'plan_pago_id' => $planPago->id,
                'fecha' => '2025-01-15',
            ],
            [
                'total' => 5000.00,
                'estado' => 'completado',
                'metodo_pago' => 'Transferencia Bancaria',
                'transaccion_id' => 'TRX-001-2025',
            ]
        );

        Pago::firstOrCreate(
            [
                'plan_pago_id' => $planPago->id,
                'fecha' => '2025-02-15',
            ],
            [
                'total' => 5000.00,
                'estado' => 'pendiente',
                'metodo_pago' => 'Efectivo',
            ]
        );

        Pago::firstOrCreate(
            [
                'plan_pago_id' => $planPago->id,
                'fecha' => '2025-03-15',
            ],
            [
                'total' => 5000.00,
                'estado' => 'pendiente',
                'metodo_pago' => 'Efectivo',
            ]
        );

        $this->command->info('Test client created successfully!');
        $this->command->info('Email: cliente@test.com');
        $this->command->info('Password: password123');
    }
}
