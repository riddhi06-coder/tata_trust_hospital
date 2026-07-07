<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('join_page_common_rows', function (Blueprint $table) {
            $table->id();

            $table->foreignId('join_page_id')
                ->constrained('join_pages')
                ->cascadeOnDelete();

            $table->string('job_title');
            $table->string('subject');
            $table->text('description')->nullable();
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
        Schema::dropIfExists('join_page_common_rows');
    }
};
