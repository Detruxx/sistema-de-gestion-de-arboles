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
        Schema::create('company_roles', function (Blueprint $table) {
            $table->id();
            // Si se borra la empresa, se borran sus roles automáticamente
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            
            $table->string('job_role'); // Ej: 'Poda', 'Extracción', 'Fitosanitario'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_roles');
    }
};
