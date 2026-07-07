<?php

namespace Database\Factories;

use App\Models\WorkOrder;
use App\Models\Request;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkOrderFactory extends Factory
{
    protected $model = WorkOrder::class;

    public function definition(): array
    {
        // 1. Primero definimos el estado de la obra aleatoriamente como ya lo hacías
        $workStatus = $this->faker->randomElement(['En espera', 'Asignado', 'En Proceso', 'Finalizado']);

        // 📍 LÓGICA DE NEGOCIO PARA EL PAGO: 
        // Si está finalizado, puede estar pago o no. Si no se terminó, siempre queda 'Pendiente'.
        $paymentStatus = ($workStatus === 'Finalizado') 
            ? $this->faker->randomElement(['Pendiente', 'Pagado']) 
            : 'Pendiente';

        return [
            // Elige un reclamo y una empresa al azar de los que ya existan en la base de datos
            'request_id'       => Request::inRandomOrder()->first()->id ?? 1,
            'company_id'       => Company::inRandomOrder()->first()->id ?? 1,
            'task_description' => $this->faker->randomElement([
                'Poda de reducción de copa por interferencia de cables.',
                'Extracción de árbol seco con peligro de caída.',
                'Tratamiento fitosanitario contra plagas en follaje.',
                'Despeje de luminarias y señalética vial.',
                'Recolección de ramas y residuos de poda acopiados.'
            ]),
            'scheduled_date'   => $this->faker->dateTimeBetween('now', '+15 days')->format('Y-m-d'),
            'execution_order'  => $this->faker->numberBetween(1, 5),
            'work_status'      => $workStatus,

            // Estado de pago de la orden (Pedido por el frente)
            'payment_status'   => $paymentStatus,

            // Costo monetario simulado de la obra (entre $10.000 y $250.000 con 2 decimales)
            'cost'             => $this->faker->randomFloat(2, 10000, 250000),
        ];
    }
}