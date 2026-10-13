<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Patient ID / UID collected from existing clients in the WhatsApp booking flow.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_booking_requests', function (Blueprint $table) {
            $table->string('patient_uid', 60)->nullable()->after('mobile');
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_booking_requests', function (Blueprint $table) {
            $table->dropColumn('patient_uid');
        });
    }
};
