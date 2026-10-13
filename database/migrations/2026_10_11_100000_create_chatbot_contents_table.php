<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Editable content for the WhatsApp chatbot. Each row is one piece of
 * admin-manageable copy — a question prompt, an answer option set, a menu label,
 * or an info block. The chatbot reads these at runtime and falls back to its
 * built-in defaults when a key is missing/inactive, so the flow never breaks.
 *
 * The conversation STRUCTURE (step order + branching) stays in code; admins edit
 * the wording, the option labels + order, and can enable/disable optional items.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_contents', function (Blueprint $table) {
            $table->id();
            $table->string('section', 60)->index();     // admin grouping: Booking / Report / Menu / Info / FAQ
            $table->string('content_key', 100)->unique(); // code reference (protected)
            $table->string('title');                     // admin-facing label
            $table->text('body')->nullable();            // the prompt / message text
            $table->json('options')->nullable();         // [{id,title,description?,sort}] for choice/menu/faq
            $table->boolean('has_options')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_protected')->default(true); // core keys: can't delete, key is read-only
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_contents');
    }
};
