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
        Schema::table('request_statuses', function (Blueprint $table) {
            $table->integer('sequence')->nullable()->after('slug');
            $table->boolean('is_terminal')->default(false)->after('sequence');
            $table->string('color', 50)->default('primary')->after('is_terminal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_statuses', function (Blueprint $table) {
            $table->dropColumn(['sequence', 'is_terminal', 'color']);
        });
    }
};
