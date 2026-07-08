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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');          // Nombre fantasía
            $table->string('business_name'); // Razón social
            $table->string('cuit')->unique();
            $table->string('email')->unique();
            $table->string('location');      // Dirección o zona de cobertura
            $table->foreignId('user_status_id')->default(1)->constrained('user_statuses'); //Conexión con la tabla de estados de usuario/empresa 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};