<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Stamps `deleted_by` with the currently authenticated user's id whenever a
 * model is deleted (including soft deletes). Skips force-deletes since the
 * row will be gone anyway.
 *
 * Pair with `SoftDeletes` for the standard soft-delete + audit flow.
 */
trait TracksDeletedBy
{
    public static function bootTracksDeletedBy(): void
    {
        static::deleting(function (Model $model): void {
            if (method_exists($model, 'isForceDeleting') && $model->isForceDeleting()) {
                return;
            }

            if (! Auth::check()) {
                return;
            }

            // Write deleted_by in its own UPDATE so it lands alongside the
            // soft-delete's UPDATE without triggering Eloquent save events.
            DB::table($model->getTable())
                ->where($model->getKeyName(), $model->getKey())
                ->update(['deleted_by' => Auth::id()]);

            $model->setAttribute('deleted_by', Auth::id());
        });
    }
}
