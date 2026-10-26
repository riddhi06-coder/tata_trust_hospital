<?php

namespace Database\Seeders;

use App\Models\ChatbotContent;
use Illuminate\Database\Seeder;

/**
 * Applies the Oct 2026 chatbot content changes to EXISTING rows. The main
 * ChatbotContentSeeder uses firstOrCreate (so it never clobbers admin edits),
 * which means content tweaks won't reach already-seeded environments. This
 * seeder force-updates the specific rows involved in this change set.
 *
 * Idempotent — safe to run multiple times.
 */
class ChatbotContentUpdateOct2026Seeder extends Seeder
{
    public function run(): void
    {
        // 1. Main menu — remove "Common questions" (menu_faq) and "Blog & articles" (menu_blog).
        $this->setOptions('menu.items', [
            ['id' => 'menu_book',      'title' => '📅 Book appointment', 'description' => 'Reserve a visit for your pet',  'sort' => 0, 'is_active' => true],
            ['id' => 'menu_services',  'title' => '🏥 Our services',     'description' => 'Departments & specialities',    'sort' => 1, 'is_active' => true],
            ['id' => 'menu_timings',   'title' => '📍 Timings & location', 'description' => 'Hours, address & directions', 'sort' => 2, 'is_active' => true],
            ['id' => 'menu_emergency', 'title' => '🚨 Emergency help',   'description' => 'Urgent care for your pet',      'sort' => 3, 'is_active' => true],
            ['id' => 'menu_reports',   'title' => '📄 Medical reports',  'description' => "Request your pet's reports",    'sort' => 4, 'is_active' => true],
            ['id' => 'menu_talk',      'title' => '💬 Talk to our team', 'description' => 'Speak with a real person',      'sort' => 5, 'is_active' => true],
        ]);

        // 2. Timings — no "Working Hours" heading; new IPD/OPD wording.
        $this->setBody('info.hours', "🚑 *IPD & Emergency:* 24×7\n🕐 *OPD & Consultations:* 9:30 AM – 7:30 PM, all days (Monday–Sunday)");

        // 3. Booking + report — drop "Best" from the mobile prompt.
        $this->setBody('book.mobile', '*Mobile number* to reach you?');
        $this->setBody('report.mobile', '*Mobile number* to reach you?');

        // 3b. Booking — drop "reason" from the main concern question.
        $this->setBody('book.complaint', "What's the *main concern* for the visit?");

        // 3c. Visit reason — remove the "Dental / other" option.
        $this->setOptions('book.reason', [
            ['id' => 'reason_vac', 'title' => '💉 Vaccination',           'sort' => 0, 'is_active' => true],
            ['id' => 'reason_gen', 'title' => '🩺 General consultation',  'sort' => 1, 'is_active' => true],
            ['id' => 'reason_sur', 'title' => '🔬 Surgery / procedure',   'sort' => 2, 'is_active' => true],
        ]);

        // 3d. Doctor step removed from the flow — hide its (now unused) content row.
        ChatbotContent::where('content_key', 'book.doctor')->update(['is_active' => false]);

        ChatbotContent::flush();
        $this->command?->info('Chatbot content (Oct 2026) updates applied.');
    }

    private function setBody(string $key, string $body): void
    {
        ChatbotContent::where('content_key', $key)->update(['body' => $body]);
    }

    private function setOptions(string $key, array $options): void
    {
        ChatbotContent::where('content_key', $key)->update([
            'options'     => $options,
            'has_options' => true,
        ]);
    }
}
