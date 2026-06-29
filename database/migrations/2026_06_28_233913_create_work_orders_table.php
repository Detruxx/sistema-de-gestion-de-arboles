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
            $table->string('task_description'); // Ej: "Corte de raíz" o "Hacer vereda"
            $table->date('scheduled_date')->nullable(); // Fecha que pacta la empresa
            $table->enum('work_status', ['Asignado', 'En Proceso', 'Finalizado'])->default('Asignado');
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
