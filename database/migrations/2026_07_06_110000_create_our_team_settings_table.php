<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('our_team_settings', function (Blueprint $table) {
            $table->id();

            // Banner
            $table->string('banner_heading')->nullable();
            $table->string('banner_image')->nullable();

            // Intro section (rich text)
            $table->string('section_heading')->nullable();
            $table->longText('section_description')->nullable();

            // Motto
            $table->string('motto')->nullable();
            $table->longText('motto_description')->nullable();
            $table->string('motto_image')->nullable();

            // Board section
            $table->string('board_heading')->nullable();
            $table->text('board_small_desc')->nullable();
            $table->string('board_image')->nullable();
            $table->string('board_name')->nullable();
            $table->string('board_designation')->nullable();
            $table->json('board_members')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('our_team_settings');
    }
};
