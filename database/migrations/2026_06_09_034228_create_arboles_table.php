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
            $table->id('id_arboles');
            $table->foreignId('id_especies')->constrained()->onDelete('cascade');
            $table->float('altura');
            $table->float('dap');
            $table->string('estado');
            $table->foreignId('id_plantera')->constrained()->onDelete('cascade');
            $table->string('observaciones');
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
