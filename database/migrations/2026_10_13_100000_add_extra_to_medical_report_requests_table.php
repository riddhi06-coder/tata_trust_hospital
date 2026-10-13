<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Stores answers to admin-created custom questions asked in the medical-report
 * flow (label => answer), so nothing collected in chat is lost.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_report_requests', function (Blueprint $table) {
            $table->json('extra')->nullable()->after('report_name');
        });
    }

    public function down(): void
    {
        Schema::table('medical_report_requests', function (Blueprint $table) {
            $table->dropColumn('extra');
        });
    }
};
