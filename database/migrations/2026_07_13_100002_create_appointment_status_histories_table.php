<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment_status_histories', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('appointment_enquiry_id');
            $table->foreign('appointment_enquiry_id')
                  ->references('id')->on('appointment_enquiries')
                  ->cascadeOnDelete();

            $table->unsignedBigInteger('from_status_id')->nullable();
            $table->unsignedBigInteger('to_status_id')->nullable();

            $table->text('note')->nullable();          // optional reason / diagnosis note

            $table->unsignedBigInteger('changed_by')->nullable(); // admin user id
            $table->string('changed_by_name')->nullable();        // snapshot of admin name

            $table->timestamp('created_at')->nullable();

            $table->index('appointment_enquiry_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_status_histories');
    }
};
