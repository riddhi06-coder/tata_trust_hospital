<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Medical-report requests captured through the WhatsApp chatbot. The pet parent
 * gives just enough to identify the patient + which report they need; the team
 * then follows up with the document. Listed in the admin panel for follow-up.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_report_requests', function (Blueprint $table) {
            $table->id();
            $table->string('wa_id', 20)->index();   // sender phone (E.164 digits)
            $table->string('client_name')->nullable();
            $table->string('patient_name')->nullable();
            $table->string('patient_uid', 60)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('report_name')->nullable();
            $table->string('status', 20)->default('new'); // new | contacted | closed
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_report_requests');
    }
};
