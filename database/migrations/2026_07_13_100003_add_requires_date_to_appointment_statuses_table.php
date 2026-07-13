<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointment_statuses', function (Blueprint $table) {
            // When true, selecting this status forces the admin to pick a new
            // appointment date (used for "Rescheduled").
            $table->boolean('requires_appointment_date')->default(false)->after('is_default');
        });

        // Mark the seeded "Rescheduled" status.
        DB::table('appointment_statuses')
            ->where('slug', 'rescheduled')
            ->update(['requires_appointment_date' => true]);
    }

    public function down(): void
    {
        Schema::table('appointment_statuses', function (Blueprint $table) {
            $table->dropColumn('requires_appointment_date');
        });
    }
};
