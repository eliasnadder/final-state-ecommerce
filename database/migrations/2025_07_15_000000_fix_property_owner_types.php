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
        // Fix any existing properties with incorrect owner_type values
        DB::table('properties')
            ->where('owner_type', 'Office')
            ->update(['owner_type' => 'App\\Models\\Office']);
            
        DB::table('properties')
            ->where('owner_type', 'User')
            ->update(['owner_type' => 'App\\Models\\User']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to simple class names if needed
        DB::table('properties')
            ->where('owner_type', 'App\\Models\\Office')
            ->update(['owner_type' => 'Office']);
            
        DB::table('properties')
            ->where('owner_type', 'App\\Models\\User')
            ->update(['owner_type' => 'User']);
    }
};
