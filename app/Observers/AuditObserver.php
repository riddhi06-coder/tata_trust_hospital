<?php

namespace App\Observers;

use App\Support\ActivityLogger;
use Illuminate\Database\Eloquent\Model;

/**
 * A single observer attached to every auditable model. Records create / update
 * / delete activity, and classifies the project's "soft delete via update"
 * pattern ($model->update(['deleted_by' => ...])) as a delete/restore.
 *
 * One log per operation:
 *  - update()-based soft delete fires `updated` only  -> classified here.
 *  - SoftDeletes ->delete() fires `deleted` only       -> deleted() below.
 *  These never both fire for the same operation, so there is no double logging.
 */
class AuditObserver
{
    /** Never diff/store these. */
    private const TIMESTAMPS = ['created_at', 'updated_at'];

    /** Store as a changed field, but hide the value. */
    private const REDACT = ['password', 'remember_token'];

    public function created(Model $model): void
    {
        $values = $this->clean($model->getAttributes());

        ActivityLogger::log('created', $model, [
            'new_values'     => $values,
            'changed_fields' => array_keys($values),
            'description'    => 'Created '.ActivityLogger::moduleLabel($model).' #'.$model->getKey(),
        ]);
    }

    public function updated(Model $model): void
    {
        $changes = $model->getChanges();
        foreach (self::TIMESTAMPS as $t) {
            unset($changes[$t]);
        }

        if (empty($changes)) {
            return;
        }

        $event = $this->classify($model, $changes);

        $new = [];
        $old = [];
        foreach ($changes as $key => $value) {
            if (in_array($key, self::REDACT, true)) {
                $new[$key] = '••••••';
                $old[$key] = '••••••';
            } else {
                $new[$key] = $value;
                $old[$key] = $model->getOriginal($key);
            }
        }

        ActivityLogger::log($event, $model, [
            'old_values'     => $old,
            'new_values'     => $new,
            'changed_fields' => array_keys($changes),
            'description'    => ucfirst($event).' '.ActivityLogger::moduleLabel($model).' #'.$model->getKey(),
        ]);
    }

    public function deleted(Model $model): void
    {
        ActivityLogger::log('deleted', $model, [
            'description' => 'Deleted '.ActivityLogger::moduleLabel($model).' #'.$model->getKey(),
        ]);
    }

    /** Decide whether an update is really a soft-delete or a restore. */
    private function classify(Model $model, array $changes): string
    {
        foreach (['deleted_by', 'deleted_at'] as $col) {
            if (! array_key_exists($col, $changes)) {
                continue;
            }
            $old = $model->getOriginal($col);
            $new = $model->getAttribute($col);

            if (is_null($old) && ! is_null($new)) {
                return 'deleted';
            }
            if (! is_null($old) && is_null($new)) {
                return 'restored';
            }
        }

        return 'updated';
    }

    private function clean(array $attributes): array
    {
        foreach (self::TIMESTAMPS as $t) {
            unset($attributes[$t]);
        }
        foreach (self::REDACT as $r) {
            if (array_key_exists($r, $attributes)) {
                $attributes[$r] = '••••••';
            }
        }

        return $attributes;
    }
}
