<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment_enquiries', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('appointment_user_id')->nullable();
            $table->foreign('appointment_user_id')
                  ->references('id')->on('appointment_users')
                  ->nullOnDelete();

            $table->string('owner_name');
            $table->string('mobile', 10);
            $table->string('email');
            $table->string('address');
            $table->string('pincode', 10);

            $table->string('pet_name');
            $table->string('pet_age')->nullable();
            $table->enum('pet_type', ['dog', 'cat']);
            $table->enum('pet_gender', ['male', 'female']);
            $table->enum('consult_type', ['first', 'followup']);

            $table->text('reason');
            $table->date('appointment_date');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_enquiries');
    }
};
