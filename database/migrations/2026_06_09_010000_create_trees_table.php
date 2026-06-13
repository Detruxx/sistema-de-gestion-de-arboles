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
        Schema::create('trees', function (Blueprint $table) {
            //DATOS PRINCIPALES
            $table->id();
            $table->foreignId('species_id')->constrained('species')->onDelete('restrict');
    
            // CONTEXTO 1: Árbol de Vereda (Alineado)
            $table->foreignId('planter_id')->nullable()->constrained('planters')->onDelete('restrict');
            $table->foreignId('street_id')->nullable()->constrained('streets')->onDelete('restrict');
            $table->string('reference')->nullable(); // Ej: "Frente a chapa 1425"

            // CONTEXTO 2: Árbol de Plaza / Espacio Verde
            $table->foreignId('park_id')->nullable()->constrained('parks')->onDelete('restrict');
            
            // Datos geográficos (OBLIGATORIOS para árboles de plaza)
            $table->double('latitude'); 
            $table->double('longitude');
            
            // Datos forestales y secundarios
            $table->decimal('height', 5, 2);
            $table->decimal('dap', 5, 2);
            $table->string('maintenance_status')->nullable();
            $table->json('vitality')->nullable();
            $table->string('structure')->nullable();
            $table->tinyInteger('degree')->nullable();
            $table->string('observations')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trees');
    }
};

