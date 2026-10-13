<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Supports admin-created custom questions in the chatbot. Built-in content keeps
 * these null; custom questions set `flow` (which conversation they belong to),
 * `field_key` (the answer key stored against the booking) and run with
 * is_protected = false so they can be deleted.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chatbot_contents', function (Blueprint $table) {
            $table->string('flow', 30)->nullable()->after('section');     // e.g. 'booking'
            $table->string('field_key', 60)->nullable()->after('content_key'); // answer key for custom questions
            $table->boolean('is_choice')->default(false)->after('has_options'); // custom question type: choice vs text
        });
    }

    public function down(): void
    {
        Schema::table('chatbot_contents', function (Blueprint $table) {
            $table->dropColumn(['flow', 'field_key', 'is_choice']);
        });
    }
};
