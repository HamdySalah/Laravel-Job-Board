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
        Schema::table('applications', function (Blueprint $table) {
            // Drop the existing enum column
            $table->dropColumn('status');
        });

        Schema::table('applications', function (Blueprint $table) {
            // Recreate the column with the new enum values
            $table->enum('status', ['pending', 'reviewing', 'interviewed', 'accepted', 'rejected'])->default('pending')->after('message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Drop the modified column
            $table->dropColumn('status');
        });

        Schema::table('applications', function (Blueprint $table) {
            // Restore the original enum values
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending')->after('message');
        });
    }
};
