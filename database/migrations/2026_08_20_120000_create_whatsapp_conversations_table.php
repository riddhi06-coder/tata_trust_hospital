<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('wa_id', 20)->unique();          // sender phone in E.164 digits (e.g. 9198...)
            $table->string('name')->nullable();              // WhatsApp profile name
            $table->string('step', 40)->default('idle');     // current step in the flow
            $table->json('data')->nullable();                // fields collected during multi-step flows
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_conversations');
    }
};
