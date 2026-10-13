<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Preferred doctor + speciality captured in the WhatsApp booking flow, so the
 * admin dashboard shows who the pet parent asked to see. Nullable — the website
 * form doesn't collect these and "No preference" is allowed on WhatsApp.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointment_enquiries', function (Blueprint $table) {
            $table->string('speciality', 150)->nullable()->after('reason');
            $table->string('preferred_doctor', 150)->nullable()->after('speciality');
        });
    }

    public function down(): void
    {
        Schema::table('appointment_enquiries', function (Blueprint $table) {
            $table->dropColumn(['speciality', 'preferred_doctor']);
        });
    }
};
