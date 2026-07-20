<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Privacy Policy becomes a multi-record "Policies" list: each row now has a
     * display name alongside its document.
     */
    public function up(): void
    {
        Schema::table('privacy_policies', function (Blueprint $table) {
            $table->string('name')->default('Privacy Policy')->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('privacy_policies', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
};
