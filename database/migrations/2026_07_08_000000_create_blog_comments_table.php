<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_comments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('blog_listing_id')
                ->constrained('blog_listings')
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('email');
            $table->string('website')->nullable();
            $table->text('comment');
            $table->boolean('is_active')->default(true);

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['blog_listing_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_comments');
    }
};
