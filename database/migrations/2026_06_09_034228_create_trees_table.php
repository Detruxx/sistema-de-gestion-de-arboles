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
            $table->foreignId('species_id')->constrained('species');
            $table->foreignId('planter_id')->constrained('planters');
            $table->decimal('height', 5, 2); // Altura en metros
            $table->decimal('dbh', 5, 2);    // Diámetro Altura Pecho (DAP) en cm
            $table->enum('status', ['pending', 'completed'])->default('pending'); // Estado de revisión
            $table->string('foliage_features')->nullable(); // Hojas / Follaje
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
