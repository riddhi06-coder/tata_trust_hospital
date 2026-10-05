<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Marks a WhatsApp booking request as "converted" once the user has completed
 * the web booking form, so re-opening the (one-time) booking link shows a
 * "already completed — start fresh" notice instead of re-prefilling it.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_booking_requests', function (Blueprint $table) {
            $table->timestamp('converted_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_booking_requests', function (Blueprint $table) {
            $table->dropColumn('converted_at');
        });
    }
};
