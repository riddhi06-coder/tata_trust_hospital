<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_us', function (Blueprint $table) {
            $table->id();

            // Banner
            $table->string('banner_heading')->nullable();
            $table->string('banner_image')->nullable();

            // About section
            $table->string('about_heading')->nullable();
            $table->longText('about_description')->nullable();
            $table->string('about_image')->nullable();
            $table->json('about_info_items')->nullable();   // [{image, heading, description}]

            // Our Values section
            $table->string('values_heading')->nullable();
            $table->string('values_image')->nullable();
            $table->longText('values_description')->nullable();

            // Reflecting Commitment section
            $table->string('commitment_heading')->nullable();
            $table->json('commitment_items')->nullable();    // [{image, count, title}]

            // Contact section
            $table->string('contact_image')->nullable();
            $table->longText('contact_description')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_us');
    }
};
