<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('our_teams', function (Blueprint $table) {
            $table->longText('bio')->nullable()->after('designation');
            $table->boolean('show_on_team_page')->default(true)->after('show_on_home');
        });
    }

    public function down(): void
    {
        Schema::table('our_teams', function (Blueprint $table) {
            $table->dropColumn(['bio', 'show_on_team_page']);
        });
    }
};
