<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->text('inspector_response')->nullable()->after('message');
            $table->boolean('is_new_for_user')->default(false)->after('inspector_response');
        });

        // Actualizar el ENUM para permitir la palabra 'answered'
        DB::statement("ALTER TABLE contact_messages MODIFY COLUMN status ENUM('unread', 'read', 'answered') DEFAULT 'unread'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropColumn(['inspector_response', 'is_new_for_user']);
        });
        DB::statement("ALTER TABLE contact_messages MODIFY COLUMN status ENUM('unread', 'read') DEFAULT 'unread'");
    }
};
