<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment_otps', function (Blueprint $table) {
            $table->id();
            $table->string('mobile', 10);
            $table->string('otp', 6);
            $table->timestamp('expires_at');
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamps();

            $table->index('mobile');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_otps');
    }
};
