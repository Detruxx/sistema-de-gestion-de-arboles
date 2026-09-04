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
        Schema::create('planters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('street_id')->constrained('streets')->onDelete('restrict');
            $table->enum('planter_state', ['empty','ocuppied', 'subocuppied', 'overocuppied', 'closed', 'partially closed'])->default('empty'); // Estado de ocupacion de la plantera
            $table->enum('position', ['in line', 'corner', 'out of line'])->nullable(); // Posicion de la plantera
            $table->enum('height', ['elevated', 'ground level', 'low level'])->default('ground level'); //Altura de la plantera
            $table->integer('street_width')->nullable(); //Ancho de la vereda
            

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planters');
    }
};
