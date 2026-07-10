<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();

            // Link to the job role (nullable so applications survive if a role is deleted).
            $table->foreignId('job_role_id')
                ->nullable()
                ->constrained('job_roles')
                ->nullOnDelete();

            $table->string('applying_for');
            $table->string('full_name');
            $table->string('email');
            $table->string('phone', 40);
            $table->string('location');
            $table->string('joining_time', 100);
            $table->text('message')->nullable();
            $table->string('resume_file');

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
