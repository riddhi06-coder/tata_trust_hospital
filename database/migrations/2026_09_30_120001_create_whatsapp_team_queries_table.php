<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * "Talk to our team" messages captured through the WhatsApp chatbot. Each is
 * forwarded to Customer Care by email and listed here for follow-up.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_team_queries', function (Blueprint $table) {
            $table->id();
            $table->string('wa_id', 20)->index();   // sender phone (E.164 digits)
            $table->string('name')->nullable();     // WhatsApp profile name
            $table->text('message');
            $table->string('status', 20)->default('new'); // new | contacted | closed
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_team_queries');
    }
};
