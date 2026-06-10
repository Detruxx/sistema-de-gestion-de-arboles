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
        Schema::create('arboles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_especies')->constrained('especies')->onDelete('restrict');
            $table->decimal('altura', 5, 2);
            $table->decimal('dap', 5, 2);
            $table->enum('estado', ['pendiente', 'concluido'])->default('pendiente');
            $table->foreignId('id_plantera')->constrained('planteras')->onDelete('restrict');
            $table->string('rasgo_follaje')->nullable();
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
