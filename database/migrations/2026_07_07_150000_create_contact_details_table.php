<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_details', function (Blueprint $table) {
            $table->id();

            $table->string('banner_heading')->nullable();
            $table->string('banner_image')->nullable();

            $table->text('address')->nullable();

            $table->string('email')->nullable();
            $table->string('footer_email')->nullable();
            $table->string('emergency_no')->nullable();
            $table->string('join_team_email')->nullable();
            $table->string('book_appointment_email')->nullable();

            $table->longText('donate_info')->nullable();

            $table->text('map_url')->nullable();
            $table->longText('iframe_url')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_details');
    }
};
