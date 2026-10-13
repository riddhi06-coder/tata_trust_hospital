<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One piece of admin-editable chatbot copy. The bot reads these via the static
 * helpers below, which cache all active rows for the request and fall back to
 * the caller-supplied defaults when a key is absent/inactive.
 */
class ChatbotContent extends Model
{
    protected $table = 'chatbot_contents';

    protected $fillable = [
        'section', 'flow', 'content_key', 'field_key', 'title', 'body', 'options',
        'has_options', 'is_choice', 'is_active', 'is_protected', 'sort_order',
    ];

    protected $casts = [
        'options'      => 'array',
        'has_options'  => 'boolean',
        'is_choice'    => 'boolean',
        'is_active'    => 'boolean',
        'is_protected' => 'boolean',
    ];

    /** Per-request cache of active rows keyed by content_key. */
    private static ?array $bag = null;

    /** Load (once) the active rows keyed by content_key. Null-safe if table is missing. */
    private static function bag(): array
    {
        if (self::$bag !== null) {
            return self::$bag;
        }

        try {
            self::$bag = self::query()
                ->whereNull('deleted_by')
                ->where('is_active', true)
                ->get()
                ->keyBy('content_key')
                ->all();
        } catch (\Throwable $e) {
            self::$bag = [];
        }

        return self::$bag;
    }

    /** Clear the cache (call after admin edits within the same request). */
    public static function flush(): void
    {
        self::$bag = null;
    }

    /**
     * Active admin-created custom questions for a conversation flow (e.g. 'booking'),
     * ordered, as bot step arrays: ['key','type','q','choices'?].
     */
    public static function customSteps(string $flow): array
    {
        $steps = [];
        foreach (self::bag() as $row) {
            if ($row->flow !== $flow || ! $row->field_key) {
                continue;
            }
            $steps[] = $row;
        }

        usort($steps, fn ($a, $b) => $a->sort_order <=> $b->sort_order);

        return array_map(function ($row) {
            $step = [
                'key'  => $row->field_key,
                'type' => $row->is_choice ? 'choice' : 'text',
                'q'    => $row->body ?: 'Please reply:',
            ];
            if ($row->is_choice) {
                $step['list'] = true;
                $step['values'] = [];
                $choices = [];
                foreach (collect($row->options ?? [])
                    ->filter(fn ($o) => ($o['is_active'] ?? true) && trim((string) ($o['title'] ?? '')) !== '')
                    ->sortBy(fn ($o) => (int) ($o['sort'] ?? 0)) as $o) {
                    $id = $o['id'] ?? ('opt_'.count($choices));
                    $choices[] = ['id' => $id, 'title' => mb_substr((string) $o['title'], 0, 24)];
                    $step['values'][$id] = (string) $o['title'];
                }
                $step['choices'] = $choices ?: [['id' => 'opt_0', 'title' => 'OK']];
            }
            return $step;
        }, $steps);
    }

    /** Field-key => question-title map for a flow's custom questions (for labels/summaries). */
    public static function customLabels(string $flow): array
    {
        $out = [];
        foreach (self::bag() as $row) {
            if ($row->flow === $flow && $row->field_key) {
                $out[$row->field_key] = $row->title;
            }
        }
        return $out;
    }

    /** Active rows for a flow (e.g. 'menu', 'faq'), ordered by sort_order. */
    public static function flowRows(string $flow): array
    {
        $rows = [];
        foreach (self::bag() as $row) {
            if ($row->flow === $flow && $row->field_key) {
                $rows[] = $row;
            }
        }
        usort($rows, fn ($a, $b) => $a->sort_order <=> $b->sort_order);

        return $rows;
    }

    /** The active row of a flow whose field_key matches, or null. */
    public static function flowRowByField(string $flow, string $fieldKey)
    {
        foreach (self::bag() as $row) {
            if ($row->flow === $flow && $row->field_key === $fieldKey) {
                return $row;
            }
        }
        return null;
    }

    /** Editable prompt/message text for a key, or the built-in default. */
    public static function text(string $key, string $default): string
    {
        $row = self::bag()[$key] ?? null;
        $body = $row?->body;

        return ($body !== null && trim($body) !== '') ? $body : $default;
    }

    /**
     * Editable option set for a choice step, or the built-in default.
     * Returns a list of ['id' => ..., 'title' => ..., 'description' => ...?].
     * Defaults are used when the key is absent OR has no stored options.
     */
    public static function options(string $key, array $default): array
    {
        $row = self::bag()[$key] ?? null;
        if (! $row || ! $row->has_options || empty($row->options)) {
            return $default;
        }

        $opts = collect($row->options)
            ->filter(fn ($o) => ($o['is_active'] ?? true) && trim((string) ($o['title'] ?? '')) !== '')
            ->sortBy(fn ($o) => (int) ($o['sort'] ?? 0))
            ->map(function ($o) {
                $row = ['id' => $o['id'] ?? null, 'title' => (string) $o['title']];
                if (! empty($o['description'])) {
                    $row['description'] = (string) $o['description'];
                }
                return $row;
            })
            ->values()
            ->all();

        return $opts ?: $default;
    }
}
