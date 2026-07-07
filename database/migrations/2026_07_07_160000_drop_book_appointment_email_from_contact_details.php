<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_details', function (Blueprint $table) {
            $table->dropColumn('book_appointment_email');
        });
    }

    public function down(): void
    {
        Schema::table('contact_details', function (Blueprint $table) {
            $table->string('book_appointment_email')->nullable()->after('join_team_email');
        });
    }
};
