<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('join_pages', function (Blueprint $table) {
            $table->id();

            $table->string('banner_heading')->nullable();
            $table->string('banner_image')->nullable();

            $table->string('section_heading')->nullable();
            $table->text('section_description')->nullable();

            $table->string('current_job_title')->nullable();
            $table->longText('current_job_description')->nullable();

            $table->string('common_heading')->nullable();
            $table->string('common_title')->nullable();
            $table->longText('common_description')->nullable();

            $table->string('extra_background_image')->nullable();
            $table->longText('extra_description')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('join_pages');
    }
};
