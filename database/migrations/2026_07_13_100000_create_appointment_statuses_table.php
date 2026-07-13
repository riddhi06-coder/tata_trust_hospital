<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color', 20)->default('#6b7280'); // badge colour (hex)
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false); // status assigned to brand-new appointments

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // Seed a sensible default set of statuses.
        $now = now();
        DB::table('appointment_statuses')->insert([
            ['name' => 'Pending',      'slug' => 'pending',      'color' => '#f59e0b', 'sort_order' => 1, 'is_active' => true, 'is_default' => true,  'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Confirmed',    'slug' => 'confirmed',    'color' => '#0d6efd', 'sort_order' => 2, 'is_active' => true, 'is_default' => false, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Completed',    'slug' => 'completed',    'color' => '#16a34a', 'sort_order' => 3, 'is_active' => true, 'is_default' => false, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Rescheduled',  'slug' => 'rescheduled',  'color' => '#7c3aed', 'sort_order' => 4, 'is_active' => true, 'is_default' => false, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Cancelled',    'slug' => 'cancelled',    'color' => '#dc2626', 'sort_order' => 5, 'is_active' => true, 'is_default' => false, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'No Show',      'slug' => 'no-show',      'color' => '#6b7280', 'sort_order' => 6, 'is_active' => true, 'is_default' => false, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_statuses');
    }
};
