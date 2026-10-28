<?php

namespace Database\Seeders;

use App\Models\ChatbotContent;
use Illuminate\Database\Seeder;

/**
 * Seeds the admin-editable chatbot content with the bot's current built-in copy.
 * Idempotent (keyed by content_key) and non-destructive: it only fills the
 * prompt/options when a row is new, so admin edits are never overwritten on
 * re-seed. The bot falls back to its code defaults for any missing key.
 */
class ChatbotContentSeeder extends Seeder
{
    public function run(): void
    {
        $i = 0;
        foreach ($this->catalog() as $row) {
            $row['sort_order']   = $i++;
            $row['is_active']    = true;
            $row['is_protected'] = true;
            $row['has_options']  = ! empty($row['options']);

            // Don't clobber existing admin edits — only create if absent.
            ChatbotContent::firstOrCreate(
                ['content_key' => $row['content_key']],
                $row
            );
        }
    }

    /** Build choice options from [id => title] pairs. */
    private function opts(array $pairs): array
    {
        $out = [];
        $s = 0;
        foreach ($pairs as $id => $title) {
            $out[] = ['id' => $id, 'title' => $title, 'sort' => $s++, 'is_active' => true];
        }
        return $out;
    }

    private function catalog(): array
    {
        return [
            /* ---- Menu ---- */
            ['section' => 'Menu', 'content_key' => 'menu.greeting', 'title' => 'Welcome greeting',
             'body' => "Hello, and a warm welcome to *Small Animal Hospital Mumbai*! 🐾🐶🐱\n\nWe're so happy to have you and your companion here. Your pet's health and happiness mean the world to us, and I'm here to help you every step of the way. 🐾\n\nHow may I assist you and your furry friend today?",
             'options' => null],
            ['section' => 'Menu', 'content_key' => 'menu.prompt', 'title' => 'Menu prompt', 'body' => 'Please choose an option below:', 'options' => null],
            ['section' => 'Menu', 'content_key' => 'menu.items', 'title' => 'Main menu options', 'body' => null, 'options' => [
                ['id' => 'menu_book',      'title' => '📅 Book appointment',   'description' => 'Reserve a visit for your pet',  'sort' => 0, 'is_active' => true],
                ['id' => 'menu_services',  'title' => '🏥 Our services',       'description' => 'Departments & specialities',    'sort' => 1, 'is_active' => true],
                ['id' => 'menu_timings',   'title' => '📍 Timings & location', 'description' => 'Hours, address & directions',   'sort' => 2, 'is_active' => true],
                ['id' => 'menu_emergency', 'title' => '🚨 Emergency help',     'description' => 'Urgent care for your pet',      'sort' => 3, 'is_active' => true],
                ['id' => 'menu_reports',   'title' => '📄 Medical reports',    'description' => "Request your pet's reports",    'sort' => 4, 'is_active' => true],
                ['id' => 'menu_talk',      'title' => '💬 Talk to our team',   'description' => 'Speak with a real person',      'sort' => 5, 'is_active' => true],
            ]],

            /* ---- Booking questions ---- */
            ['section' => 'Booking', 'content_key' => 'book.parent_name', 'title' => 'Pet parent name', 'body' => "What's the *pet parent's full name*?", 'options' => null],
            ['section' => 'Booking', 'content_key' => 'book.address', 'title' => 'Address', 'body' => "Your *address*?", 'options' => null],
            ['section' => 'Booking', 'content_key' => 'book.email', 'title' => 'Email', 'body' => "Your *email address*?", 'options' => null],
            ['section' => 'Booking', 'content_key' => 'book.mobile', 'title' => 'Mobile number', 'body' => "*Mobile number* to reach you?", 'options' => null],
            ['section' => 'Booking', 'content_key' => 'book.pincode', 'title' => 'PIN code', 'body' => "Your *PIN code*?", 'options' => null],
            ['section' => 'Booking', 'content_key' => 'book.patient_uid', 'title' => 'Patient UID', 'body' => "Great — please share your *Patient ID / UID*.", 'options' => null],
            ['section' => 'Booking', 'content_key' => 'book.pet_name', 'title' => 'Pet name', 'body' => "Now your companion 🐾 — what's your *pet's name*?", 'options' => null],
            ['section' => 'Booking', 'content_key' => 'book.species', 'title' => 'Species', 'body' => "Which pet are we seeing?", 'options' => $this->opts(['sp_dog' => '🐶 Dog', 'sp_cat' => '🐱 Cat'])],
            ['section' => 'Booking', 'content_key' => 'book.sex', 'title' => 'Sex', 'body' => "Your pet's *sex*?", 'options' => $this->opts(['sex_m' => '♂️ Male', 'sex_f' => '♀️ Female'])],
            ['section' => 'Booking', 'content_key' => 'book.breed', 'title' => 'Breed', 'body' => "*Breed*? (type 'NA' if unsure)", 'options' => null],
            ['section' => 'Booking', 'content_key' => 'book.colour', 'title' => 'Colour', 'body' => "*Colour*?", 'options' => null],
            ['section' => 'Booking', 'content_key' => 'book.dob_age', 'title' => 'Date of birth / age', 'body' => "*Date of birth or age*?", 'options' => null],
            ['section' => 'Booking', 'content_key' => 'book.weight', 'title' => 'Weight', 'body' => "*Weight*? (type 'NA' if unsure)", 'options' => null],
            ['section' => 'Booking', 'content_key' => 'book.neutered', 'title' => 'Neutered / spayed', 'body' => "Is your pet *neutered / spayed*?", 'options' => $this->opts(['neu_y' => 'Yes', 'neu_n' => 'No'])],
            ['section' => 'Booking', 'content_key' => 'book.complaint', 'title' => 'Main concern', 'body' => "What's the *main concern* for the visit?", 'options' => null],
            ['section' => 'Booking', 'content_key' => 'book.how_heard', 'title' => 'How did you hear about us', 'body' => "How did you *hear about us*?", 'options' => $this->opts([
                'heard_google' => 'Google Search', 'heard_maps' => 'Google Maps', 'heard_social' => 'Social Media (Insta/FB)',
                'heard_friend' => 'Friend/Family Referral', 'heard_vet' => 'Veterinarian Referral', 'heard_previous' => 'Previous Visit',
                'heard_tata' => 'Tata Trusts/Tata Group', 'heard_other' => 'Other',
            ])],
            ['section' => 'Booking', 'content_key' => 'book.referred_by', 'title' => 'Referred by', 'body' => "*Referred by*? (type 'NA' if none)", 'options' => null],
            ['section' => 'Booking', 'content_key' => 'book.reason', 'title' => 'Visit reason', 'body' => "Got it. What's the visit for?", 'options' => $this->opts([
                'reason_vac' => '💉 Vaccination', 'reason_gen' => '🩺 General consultation', 'reason_sur' => '🔬 Surgery / procedure',
            ])],
            ['section' => 'Booking', 'content_key' => 'book.speciality', 'title' => 'Speciality (options from DB)', 'body' => "Which *speciality* do you need?", 'options' => null],
            ['section' => 'Booking', 'content_key' => 'book.preferred_day', 'title' => 'Preferred day', 'body' => "Which day works best for you?", 'options' => $this->opts([
                'day_today' => 'Today', 'day_tomorrow' => 'Tomorrow', 'day_pick' => '📆 Pick a date',
            ])],
            ['section' => 'Booking', 'content_key' => 'book.preferred_time', 'title' => 'Preferred time', 'body' => "And a preferred time?", 'options' => $this->opts([
                'time_morning' => '🌅 Morning (9–12)', 'time_afternoon' => '☀️ Afternoon (12–4)', 'time_evening' => '🌆 Evening (4–8)',
            ])],

            /* ---- Medical report questions ---- */
            ['section' => 'Medical Report', 'content_key' => 'report.client_name', 'title' => 'Client name', 'body' => "*Medical Reports* 📄\n\nHappy to help you get your pet's reports. First, the *pet parent's name*?", 'options' => null],
            ['section' => 'Medical Report', 'content_key' => 'report.patient_name', 'title' => 'Patient name', 'body' => "Your *pet's name*?", 'options' => null],
            ['section' => 'Medical Report', 'content_key' => 'report.patient_uid', 'title' => 'Patient UID', 'body' => "Your *Patient ID / UID* with us? (type 'NA' if you don't have it)", 'options' => null],
            ['section' => 'Medical Report', 'content_key' => 'report.mobile', 'title' => 'Mobile number', 'body' => "*Mobile number* to reach you?", 'options' => null],
            ['section' => 'Medical Report', 'content_key' => 'report.report_name', 'title' => 'Report needed', 'body' => "Which *report* do you need? (e.g. blood test, X-ray, discharge summary)", 'options' => null],

            /* ---- Info blocks ---- */
            ['section' => 'Info', 'content_key' => 'info.hours', 'title' => 'Working hours', 'body' => "🚑 *IPD & Emergency:* 24×7\n🕐 *OPD & Consultations:* 9:30 AM – 7:30 PM, all days (Monday–Sunday)", 'options' => null],
            ['section' => 'Info', 'content_key' => 'info.emergency', 'title' => 'Emergency intro', 'body' => "*Emergency Help* 🚨\n\nYour pet's wellbeing can't wait — we're here for you. Please contact us right away.", 'options' => null],

            /* ---- FAQ answers ---- */
            ['section' => 'FAQ', 'content_key' => 'faq.faq_fees', 'title' => 'FAQ · Consultation fees', 'body' => "Consultation fees vary by service. Our team will happily share the exact charges when they call you. For immediate help, please call 022-6538-3538.", 'options' => null],
            ['section' => 'FAQ', 'content_key' => 'faq.faq_reports', 'title' => 'FAQ · Medical reports', 'body' => "Absolutely — your pet's reports can be collected at the hospital or shared with you digitally. Our reception team will be glad to help.", 'options' => null],
            ['section' => 'FAQ', 'content_key' => 'faq.faq_bring', 'title' => 'FAQ · What to bring', 'body' => "Great question! Please carry:\n• Your pet's previous prescriptions or reports\n• Vaccination card, if any\n• A leash or carrier for safe travel", 'options' => null],
            ['section' => 'FAQ', 'content_key' => 'faq.faq_parking', 'title' => 'FAQ · Parking', 'body' => "Limited parking is available within the building premises on a first-come, first-served basis.", 'options' => null],
        ];
    }
}
