<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('speciality_details', function (Blueprint $table) {
            // Normal (non-preventive) fields become nullable so a Preventive Care
            // record — which hides them — can still be saved.
            $table->string('section_image')->nullable()->change();
            $table->string('section_heading')->nullable()->change();
            $table->longText('section_description')->nullable()->change();
            $table->string('service_heading')->nullable()->change();
            $table->json('services')->nullable()->change();

            // Preventive Care layout fields.
            $table->boolean('is_preventive')->default(false)->after('speciality_id');
            $table->string('preventive_section_heading')->nullable()->after('short_info');
            $table->longText('preventive_section_description')->nullable()->after('preventive_section_heading');
            $table->json('preventive_services')->nullable()->after('preventive_section_description'); // [{image, name}]
            $table->string('preventive_plans_heading')->nullable()->after('preventive_services');
            $table->longText('preventive_plans_description')->nullable()->after('preventive_plans_heading');
            $table->json('preventive_plans')->nullable()->after('preventive_plans_description'); // [{image, name, age_range, cost}]
            $table->longText('preventive_disclaimer')->nullable()->after('preventive_plans');
        });
    }

    public function down(): void
    {
        Schema::table('speciality_details', function (Blueprint $table) {
            $table->dropColumn([
                'is_preventive',
                'preventive_section_heading',
                'preventive_section_description',
                'preventive_services',
                'preventive_plans_heading',
                'preventive_plans_description',
                'preventive_plans',
                'preventive_disclaimer',
            ]);
        });
    }
};
