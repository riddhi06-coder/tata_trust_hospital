<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_ribbon_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('contact_details_id')
                ->constrained('contact_details')
                ->cascadeOnDelete();

            $table->string('icon')->nullable();
            $table->string('title');
            $table->string('value')->nullable();
            $table->unsignedInteger('sort_order')->default(0);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_ribbon_items');
    }
};
