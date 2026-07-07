<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('requests')->onDelete('cascade');
            $table->foreignId('company_id')->constrained('companies')->onDelete('restrict');
            $table->string('task_description');
            $table->date('scheduled_date')->nullable();
            
            $table->integer('execution_order')->default(1); 
            $table->enum('work_status', ['En espera', 'Asignado', 'En Proceso', 'Finalizado'])->default('Asignado');

            // Control de costos y auditoría de pagos para el panel de empresas
            $table->string('payment_status')->default('Pendiente'); // 'Pendiente', 'Pagado'
            $table->decimal('cost', 10, 2)->default(0.00); // Registra el costo de la tarea ejecutada
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
