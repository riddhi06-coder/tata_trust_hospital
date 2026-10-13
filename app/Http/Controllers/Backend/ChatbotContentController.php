<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ChatbotContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Admin management of the WhatsApp chatbot's editable copy: question prompts,
 * answer options (+ order), menu labels, info blocks and FAQ answers. The bot
 * reads these at runtime (App\Models\ChatbotContent) and falls back to its
 * built-in defaults for any key that's missing or inactive.
 */
class ChatbotContentController extends Controller
{
    public function index()
    {
        $sections = ChatbotContent::whereNull('deleted_by')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('section');

        return view('backend.chatbot.index', compact('sections'));
    }

    public function edit($id)
    {
        $content = ChatbotContent::whereNull('deleted_by')->findOrFail($id);

        return view('backend.chatbot.edit', compact('content'));
    }

    /** Form to add a brand-new custom question to a conversation flow. */
    public function create()
    {
        return view('backend.chatbot.create');
    }

    /** Persist a new custom item: a question (booking/report), an FAQ, or a menu option. */
    public function store(Request $request)
    {
        $flow = $request->input('flow');

        $rules = [
            'title' => ['required', 'string', 'max:120'],
            'body'  => ['required', 'string', 'max:4000'],
            'flow'  => ['required', 'in:booking,report,faq,menu'],
        ];
        $rules['sort_order'] = ['nullable', 'integer', 'min:0', 'max:9999'];
        if (in_array($flow, ['booking', 'report'], true)) {
            $rules['input_type']      = ['required', 'in:text,choice'];
            $rules['options']         = ['nullable', 'array'];
            $rules['options.*.title'] = ['nullable', 'string', 'max:120'];
        }

        Validator::make($request->all(), $rules, [
            'title.required' => 'Please enter a short name.',
            'body.required'  => 'Please enter the message text.',
        ])->validate();

        // FAQ + menu are plain content (no answer options); questions may be choice.
        $isQuestion = in_array($flow, ['booking', 'report'], true);
        $isChoice   = $isQuestion && $request->input('input_type') === 'choice';
        $options    = $isChoice ? $this->parseOptions($request, 0) : null;

        if ($isChoice && empty($options)) {
            return back()->withInput()->withErrors(['options' => 'Add at least one answer option for a multiple-choice question.']);
        }

        $section = match ($flow) {
            'faq'   => 'FAQ',
            'menu'  => 'Menu',
            default => 'Custom Questions',
        };

        ChatbotContent::create([
            'section'      => $section,
            'flow'         => $flow,
            'content_key'  => 'custom.'.$flow.'.'.Str::lower(Str::random(8)),
            'field_key'    => $this->uniqueFieldKey($flow, $request->input('title')),
            'title'        => $request->input('title'),
            'body'         => $request->input('body'),
            'options'      => $options,
            'has_options'  => $isChoice,
            'is_choice'    => $isChoice,
            'is_active'    => true,
            'is_protected' => false,
            'sort_order'   => $request->filled('sort_order')
                ? (int) $request->input('sort_order')
                : (int) ChatbotContent::where('flow', $flow)->max('sort_order') + 1,
        ]);
        ChatbotContent::flush();

        $what = match ($flow) {
            'faq'  => 'FAQ',
            'menu' => 'Menu option',
            default => 'Custom question',
        };

        return redirect()->route('manage-chatbot.index')->with('message', $what.' added successfully.');
    }

    /** Delete a custom (non-protected) question. */
    public function destroy($id)
    {
        $content = ChatbotContent::whereNull('deleted_by')->findOrFail($id);

        if ($content->is_protected) {
            return back()->with('error', 'Built-in content cannot be deleted — you can hide it instead.');
        }

        $content->update(['deleted_by' => auth()->id()]);
        ChatbotContent::flush();

        return redirect()->route('manage-chatbot.index')->with('message', 'Custom question deleted.');
    }

    /** Build an options array from the submitted rows (in submission order). */
    private function parseOptions(Request $request, int $baseId): array
    {
        $rows = [];
        $sort = 0;
        foreach ((array) $request->input('options', []) as $opt) {
            $title = trim((string) ($opt['title'] ?? ''));
            if ($title === '') {
                continue;
            }
            $entry = [
                'id'        => trim((string) ($opt['id'] ?? '')) ?: 'opt_'.$baseId.'_'.$sort,
                'title'     => $title,
                'sort'      => $sort,
                'is_active' => ! empty($opt['is_active']),
            ];
            $desc = trim((string) ($opt['description'] ?? ''));
            if ($desc !== '') {
                $entry['description'] = $desc;
            }
            $rows[] = $entry;
            $sort++;
        }
        return $rows;
    }

    /**
     * Derive a stable, unique field key from the title. The prefix must route
     * correctly in the bot: FAQs start with "faq_", menu options with "menu_c_".
     */
    private function uniqueFieldKey(string $flow, string $title): string
    {
        $slug   = Str::limit(Str::slug($title, '_') ?: 'item', 36, '');
        $prefix = match ($flow) {
            'faq'  => 'faq_',
            'menu' => 'menu_c_',
            default => 'q_',
        };
        $base = $prefix.$slug;
        $key  = $base;
        $i    = 1;
        while (ChatbotContent::where('field_key', $key)->exists()) {
            $key = $base.'_'.(++$i);
        }
        return $key;
    }

    public function update(Request $request, $id)
    {
        $content = ChatbotContent::whereNull('deleted_by')->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title'                 => ['nullable', 'string', 'max:120'],
            'sort_order'            => ['nullable', 'integer', 'min:0', 'max:9999'],
            'body'                  => ['nullable', 'string', 'max:4000'],
            'is_active'             => ['nullable', 'boolean'],
            'options'               => ['nullable', 'array'],
            'options.*.id'          => ['nullable', 'string', 'max:60'],
            'options.*.title'       => ['nullable', 'string', 'max:120'],
            'options.*.description' => ['nullable', 'string', 'max:160'],
        ], [
            'options.*.title.max'       => 'Option labels must be 120 characters or less (WhatsApp shows ~24).',
            'options.*.description.max' => 'Option descriptions must be 160 characters or less.',
        ]);

        $validator->validate();

        $data = [
            'body'      => $request->input('body'),
            'is_active' => (bool) $request->boolean('is_active'),
        ];

        // Title + priority are editable only for custom items (FAQ question / menu label).
        if (! $content->is_protected) {
            if ($request->filled('title')) {
                $data['title'] = $request->input('title');
            }
            if ($request->filled('sort_order')) {
                $data['sort_order'] = (int) $request->input('sort_order');
            }
        }

        // Rebuild the options list only for rows that actually have options.
        if ($content->has_options) {
            $data['options'] = $this->parseOptions($request, $content->id);
        }

        $content->update($data);
        ChatbotContent::flush();

        return redirect()
            ->route('manage-chatbot.index')
            ->with('message', 'Chatbot content updated successfully.');
    }
}
