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
            // Which transactional SMS to fire when this status is applied:
            // null (none), 'cancellation', or 'reschedule'.
            $table->string('sms_trigger', 20)->nullable()->after('requires_appointment_date');
        });

        DB::table('appointment_statuses')->where('slug', 'cancelled')->update(['sms_trigger' => 'cancellation']);
        DB::table('appointment_statuses')->where('slug', 'rescheduled')->update(['sms_trigger' => 'reschedule']);
    }

    public function down(): void
    {
        Schema::table('appointment_statuses', function (Blueprint $table) {
            $table->dropColumn('sms_trigger');
        });
    }
};
