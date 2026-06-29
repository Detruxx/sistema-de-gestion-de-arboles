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
            
            // Atributo numérico para ordenar la ejecución (1, 2, 3...)
            $table->integer('execution_order')->default(1); 
            
            // Agregamos el estado 'En espera' al ciclo de vida
            $table->enum('work_status', ['En espera', 'Asignado', 'En Proceso', 'Finalizado'])->default('Asignado');
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
