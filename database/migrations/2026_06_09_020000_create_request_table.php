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
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('tree_id')->nullable()->constrained('trees')->onDelete('restrict');
            $table->foreignId('request_type_id')->constrained('request_types')->onDelete('restrict');
            $table->foreignId('street_id')->constrained('streets')->onDelete('restrict');
            $table->text('description');
<<<<<<< HEAD
            $table->string('path'); // path que lleva a la foto
            $table->enum('status', ['open', 'in_progress', 'resolved'])->default('open');
=======
            $table->string('path')->nullable(); // path que lleva a la foto
            
            //Parte que pasa a ser completada por inspectores
            $table->foreignId('request_status_id')->constrained('request_statuses')->onDelete('restrict');
            $table->text('cancellation_reason')->nullable(); //Lugar en que el inspector justificará por que cancela el reclamo
            $table->foreignId('priority_id')->nullable()->constrained('priorities')->onDelete('restrict');

            // Relaciones de vinculación y duplicados
            $table->unsignedBigInteger('linked_to')->nullable();
            $table->unsignedBigInteger('suggested_duplicate_id')->nullable();
            $table->foreign('linked_to')->references('id')->on('requests')->onDelete('set null');
            $table->foreign('suggested_duplicate_id')->references('id')->on('requests')->onDelete('set null');
            
>>>>>>> 7df9417fa65fa2849e01939f57c5d7913c14c79a
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
