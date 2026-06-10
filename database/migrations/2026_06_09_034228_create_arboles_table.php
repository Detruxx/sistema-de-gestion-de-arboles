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


            Schema::create('trees', function (Blueprint $table) {
            //DATOS PRINCIPALES
            $table->id();
            // Clave foranea especie
            $table->foreignId('species_id')->constrained()->onDelete('restrict');
            // Clave foranea plantera
            $table->foreignId('planter_id')->nullable()->constrained()->onDelete('restrict');
            
            // NULLABLE: porque puede estar en una calle o en un parque
            $table->foreignId('street_id')->nullable()->constrained()->onDelete('restrict');
            $table->string('reference')->nullable(); // Referencia segun la chapa de la calle (si es que esta sobre una)

            $table->foreignId('park_id')->nullable()->constrained()->onDelete('restrict');
            
            // Datos geograficos
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            
            $table->decimal('height'); // altura
            $table->decimal('dap'); // diametro a la altura del pecho

            // DATOS SECUNDARIOS
            $table->string('maintenance_status')->nullable(); // si hay un reclamo pendiente, o no, etc
            $table->string('vitality')->nullable(); // vital, en mal estado, muerto
            $table->string('structure')->nullable(); //Estructura del arbol
            $table->tinyInteger('degree')->nullable(); // degree del arbol
            $table->string('observations')->nullable(); // observaciones, datos varios
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
