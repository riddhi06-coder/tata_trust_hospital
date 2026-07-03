<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('speciality_detail_team', function (Blueprint $table) {
            $table->id();
            $table->foreignId('speciality_detail_id')->constrained('speciality_details')->cascadeOnDelete();
            $table->foreignId('our_team_id')->constrained('our_teams')->cascadeOnDelete();
            $table->longText('bio_override')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['speciality_detail_id', 'our_team_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('speciality_detail_team');
    }
};
