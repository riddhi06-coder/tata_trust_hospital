<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tags each appointment with where it originated — website (default) or whatsapp.
 * Powers the separate "WhatsApp Appointments" admin tab (same management as the
 * normal Appointments tab, just scoped by source).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointment_enquiries', function (Blueprint $table) {
            $table->string('source', 20)->default('website')->after('appointment_status_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('appointment_enquiries', function (Blueprint $table) {
            $table->dropColumn('source');
        });
    }
};
