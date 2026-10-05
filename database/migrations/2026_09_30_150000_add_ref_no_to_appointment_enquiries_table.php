<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Per-source running number so each Appointments tab (website / whatsapp) has its
 * OWN reference series (A-0001…, WA-0001…) instead of sharing the global id.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointment_enquiries', function (Blueprint $table) {
            $table->unsignedInteger('ref_no')->nullable()->after('source');
        });

        // Backfill: number existing rows sequentially within each source, by id.
        foreach (['website', 'whatsapp'] as $source) {
            $i = 0;
            DB::table('appointment_enquiries')
                ->where(function ($q) use ($source) {
                    $q->where('source', $source);
                    if ($source === 'website') {
                        $q->orWhereNull('source');
                    }
                })
                ->orderBy('id')
                ->get(['id'])
                ->each(function ($row) use (&$i) {
                    $i++;
                    DB::table('appointment_enquiries')->where('id', $row->id)->update(['ref_no' => $i]);
                });
        }
    }

    public function down(): void
    {
        Schema::table('appointment_enquiries', function (Blueprint $table) {
            $table->dropColumn('ref_no');
        });
    }
};
