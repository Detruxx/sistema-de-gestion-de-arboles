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
        Schema::create('request_status_histories', function (Blueprint $table) {
            $table->id();
            
            // Relaciones cruciales
            $table->foreignId('request_id')->constrained('requests')->onDelete('cascade');
            $table->foreignId('request_status_id')->constrained('request_statuses')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict'); // Quién cambia el estado
            
            // La justificación del inspector
            $table->text('justification')->nullable(); 
            
            $table->timestamps(); // created_at nos dará el momento exacto del cambio
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_status_histories');
    }
};
