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
        Schema::create('photo__trees', function (Blueprint $table) {
            $table->id();
            $table->string('photo_path'); // Ruta de la imagen en el storage
            $table->enum('type', ['official', 'request'])->default('request'); // Tipo de foto
            
            // Relaciones
            $table->foreignId('tree_id')->nullable()->constrained('trees')->onDelete('cascade');
            $table->foreignId('request_id')->nullable()->constrained('requests')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photo__trees');
    }
};
