<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('our_teams', function (Blueprint $table) {
            $table->boolean('show_on_home')->default(false)->after('social_media_link');
        });
    }

    public function down(): void
    {
        Schema::table('our_teams', function (Blueprint $table) {
            $table->dropColumn('show_on_home');
        });
    }
};
