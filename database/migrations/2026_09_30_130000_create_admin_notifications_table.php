<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Backend activity notifications — a shared "inbox" shown in the admin dashboard
 * whenever a new enquiry, query, or appointment arrives from the website or
 * WhatsApp. Read state is shared across admins (small team / shared inbox model).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50);                 // contact_enquiry | appointment_enquiry | job_application | whatsapp_booking | whatsapp_query
            $table->string('source', 20)->default('website'); // website | whatsapp
            $table->string('title');
            $table->string('body', 500)->nullable();
            $table->string('url')->nullable();          // deep link to view the item
            $table->string('icon', 50)->nullable();
            $table->string('color', 20)->nullable();
            $table->nullableMorphs('related');          // related_type / related_id
            $table->timestamp('read_at')->nullable();
            $table->unsignedBigInteger('read_by')->nullable();
            $table->string('read_by_name')->nullable();
            $table->timestamp('last_reminded_at')->nullable();
            $table->unsignedInteger('reminder_count')->default(0);
            $table->timestamps();

            $table->index(['read_at', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
    }
};
