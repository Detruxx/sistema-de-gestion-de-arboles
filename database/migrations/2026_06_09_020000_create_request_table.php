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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();

            // Rellenado por el vecino
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('tree_id')->nullable()->constrained('trees')->onDelete('restrict');
            $table->foreignId('request_type_id')->constrained('request_types')->onDelete('restrict');
            $table->foreignId('street_id')->constrained('streets')->onDelete('restrict');
            $table->text('description');
            
            // Modificado para soportar un array JSON con múltiples fotos (hasta 3)
            $table->json('path')->nullable(); 
            
            // Completado por inspectores
            $table->foreignId('request_status_id')->constrained('request_statuses')->onDelete('restrict');
            $table->text('cancellation_reason')->nullable(); // Justificación de inspector o ciudadano
            $table->foreignId('priority_id')->nullable()->constrained('priorities')->onDelete('restrict');

            // Asignación directa de la empresa contratista al reclamo
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('set null');

            // Relaciones de vinculación y duplicados
            $table->unsignedBigInteger('linked_to')->nullable();
            $table->unsignedBigInteger('suggested_duplicate_id')->nullable();
            $table->foreign('linked_to')->references('id')->on('requests')->onDelete('set null');
            $table->foreign('suggested_duplicate_id')->references('id')->on('requests')->onDelete('set null');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
