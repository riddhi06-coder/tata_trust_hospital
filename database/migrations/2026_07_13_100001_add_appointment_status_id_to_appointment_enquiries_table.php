<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointment_enquiries', function (Blueprint $table) {
            $table->unsignedBigInteger('appointment_status_id')->nullable()->after('appointment_date');
            $table->foreign('appointment_status_id')
                  ->references('id')->on('appointment_statuses')
                  ->nullOnDelete();
        });

        // Backfill existing appointments with the default status.
        $defaultId = DB::table('appointment_statuses')
            ->where('is_default', true)
            ->value('id');

        if ($defaultId) {
            DB::table('appointment_enquiries')
                ->whereNull('appointment_status_id')
                ->update(['appointment_status_id' => $defaultId]);
        }
    }

    public function down(): void
    {
        Schema::table('appointment_enquiries', function (Blueprint $table) {
            $table->dropForeign(['appointment_status_id']);
            $table->dropColumn('appointment_status_id');
        });
    }
};
