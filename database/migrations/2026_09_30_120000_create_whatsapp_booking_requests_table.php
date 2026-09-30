<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Completed appointment-booking intakes collected through the WhatsApp chatbot
 * (the full New/Existing-client flow). Read-only in the admin — these are lead
 * records the Customer Care team follows up on by phone.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_booking_requests', function (Blueprint $table) {
            $table->id();
            $table->string('wa_id', 20)->index();            // sender phone (E.164 digits)
            $table->string('client_type', 20)->nullable();   // new | existing

            // Pet parent
            $table->string('parent_name')->nullable();
            $table->text('address')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile', 30)->nullable();
            $table->string('pincode', 12)->nullable();

            // Pet
            $table->string('pet_name')->nullable();
            $table->string('species', 30)->nullable();
            $table->string('sex', 20)->nullable();
            $table->string('breed')->nullable();
            $table->string('colour')->nullable();
            $table->string('dob_age')->nullable();
            $table->string('weight', 30)->nullable();
            $table->string('neutered', 10)->nullable();
            $table->text('complaint')->nullable();
            $table->string('how_heard')->nullable();
            $table->string('referred_by')->nullable();

            // Visit scheduling
            $table->string('reason')->nullable();
            $table->string('preferred_day')->nullable();
            $table->string('preferred_time')->nullable();

            $table->string('status', 20)->default('new');    // new | contacted | closed
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_booking_requests');
    }
};
