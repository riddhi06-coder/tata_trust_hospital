<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lets admins choose which doctors appear as selectable in the WhatsApp booking
 * flow. Defaults to true so every existing doctor is bookable out of the box.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('our_teams', function (Blueprint $table) {
            $table->boolean('bookable_on_whatsapp')->default(true)->after('show_on_team_page');
        });
    }

    public function down(): void
    {
        Schema::table('our_teams', function (Blueprint $table) {
            $table->dropColumn('bookable_on_whatsapp');
        });
    }
};
