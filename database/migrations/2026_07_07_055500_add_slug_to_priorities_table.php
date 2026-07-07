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
        // Cleanup dirty database state from failed first run (if ID 5 exists)
        DB::table('requests')->where('priority_id', 5)->update(['priority_id' => 1]);
        DB::table('priorities')->where('id', 5)->delete();

        // 1. Add slug column as nullable if it doesn't exist
        if (!Schema::hasColumn('priorities', 'slug')) {
            Schema::table('priorities', function (Blueprint $table) {
                $table->string('slug')->nullable()->after('priority_name');
            });
        }

        // 2. Populate slugs for default priorities
        DB::table('priorities')->where('priority_name', 'Baja')->update(['slug' => 'low']);
        DB::table('priorities')->where('priority_name', 'Media')->update(['slug' => 'medium']);
        DB::table('priorities')->where('priority_name', 'Alta')->update(['slug' => 'high']);
        DB::table('priorities')->where('priority_name', 'Urgente')->update(['slug' => 'urgent']);

        // 3. Clean up duplicate 'low' priority if it exists (migrate requests to Baja ID 1 first)
        $lowPriority = DB::table('priorities')->where('priority_name', 'low')->first();
        if ($lowPriority) {
            DB::table('requests')->where('priority_id', $lowPriority->id)->update(['priority_id' => 1]);
            DB::table('priorities')->where('id', $lowPriority->id)->delete();
        }

        // 4. Create auto-alta and auto-media priorities if they do not exist
        $autoAlta = DB::table('priorities')->where('slug', 'auto-alta')->first();
        if (!$autoAlta) {
            DB::table('priorities')->insert([
                'priority_name' => 'Auto-Alta',
                'slug' => 'auto-alta',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $autoMedia = DB::table('priorities')->where('slug', 'auto-media')->first();
        if (!$autoMedia) {
            DB::table('priorities')->insert([
                'priority_name' => 'Auto-Media',
                'slug' => 'auto-media',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 5. Make slug unique
        Schema::table('priorities', function (Blueprint $table) {
            $table->string('slug')->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('priorities', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
        
        DB::table('priorities')->whereIn('slug', ['auto-alta', 'auto-media'])->delete();
    }
};
