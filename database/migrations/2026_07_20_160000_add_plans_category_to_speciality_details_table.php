<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('speciality_details', function (Blueprint $table) {
            $table->string('preventive_plans_category')->nullable()->after('preventive_plans_heading');
        });
    }

    public function down(): void
    {
        Schema::table('speciality_details', function (Blueprint $table) {
            $table->dropColumn('preventive_plans_category');
        });
    }
};
