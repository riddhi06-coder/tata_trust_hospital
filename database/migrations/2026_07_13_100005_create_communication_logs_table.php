<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('communication_logs', function (Blueprint $table) {
            $table->id();

            $table->string('channel', 10);              // sms | email
            $table->string('type', 50);                 // otp, appointment_confirmation, cancellation, ...
            $table->string('recipient');                // mobile number or email address
            $table->string('recipient_name')->nullable();

            $table->string('subject')->nullable();      // email subject
            $table->text('message')->nullable();        // sms text or email summary

            $table->string('status', 10);               // sent | failed
            $table->text('error')->nullable();          // failure reason
            $table->text('provider_response')->nullable(); // raw gateway/SMTP response

            // Loosely linked source record (AppointmentEnquiry, ContactEnquiry, ...).
            $table->string('related_type')->nullable();
            $table->unsignedBigInteger('related_id')->nullable();

            // The appointment client this message concerns (when applicable).
            $table->unsignedBigInteger('appointment_user_id')->nullable();

            // Admin who triggered it (null for automatic/frontend sends).
            $table->unsignedBigInteger('triggered_by')->nullable();
            $table->string('triggered_by_name')->nullable();

            $table->timestamp('created_at')->nullable();

            $table->index('recipient');
            $table->index('channel');
            $table->index('status');
            $table->index('type');
            $table->index('appointment_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communication_logs');
    }
};
