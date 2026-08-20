<?php

namespace App\Support;

use App\Models\ContactEnquiry;
use App\Models\OurTeam;
use App\Models\Specialities;
use App\Models\WhatsAppConversation;
use App\Services\WhatsAppFortius;
use Illuminate\Support\Facades\Log;

/**
 * Reactive WhatsApp chatbot flow for Small Animal Hospital Mumbai.
 * Runs inside the 24-hour window (user messages first → we reply free-form),
 * so NO approved templates are needed.
 *
 * Flows follow the client-approved concept, with an elegant, warm tone.
 * Services and Team are pulled LIVE from the database and link to the matching
 * website pages, so content stays in sync with the site. Items still marked
 * "[to be confirmed]" need the real hospital data (address, map link, parking).
 *
 * "Book an appointment" sends the OTP login link (per instruction) rather than
 * the full in-chat guided booking shown in the concept.
 */
class WhatsAppBot
{
    public function __construct(private WhatsAppFortius $wa) {}

    /** Words that always restart the conversation at the main menu. */
    private array $resetWords = ['hi', 'hello', 'hey', 'menu', 'start', 'main menu', 'restart'];

    /** FAQs shown under "Common questions". [short title, full question, answer]. */
    private array $faqs = [
        'faq_fees'    => ['Consultation fees', 'What are the consultation fees?', "Consultation fees vary by service. Our team will share the exact charges when they call you. For immediate help, please call 022 6538 3538."],
        'faq_reports' => ['Medical reports',   "Can I get my pet's reports?",     "Yes — your pet's reports can be collected at the hospital or shared with you digitally. Our reception team will be glad to help."],
        'faq_bring'   => ['What to bring',     'What should I bring for a visit?', "Great question! Please carry:\n• Your pet's previous prescriptions or reports\n• Vaccination card, if any\n• A leash or carrier for safe travel"],
        'faq_parking' => ['Parking',           'Is parking available?', "Parking details are being updated. [to be confirmed]"],
    ];

    public function handle(string $waId, ?string $profileName, string $text, ?string $interactiveId = null): void
    {
        $convo = WhatsAppConversation::firstOrNew(['wa_id' => $waId]);
        if ($profileName && ! $convo->name) {
            $convo->name = $profileName;
        }
        $convo->last_message_at = now();
        $convo->save();

        // An interactive tap (button/list id) wins; otherwise use the lowercased text.
        $input = $interactiveId ?: strtolower(trim($text));
        $ctx   = ['recipient_name' => $convo->name];

        if (in_array($input, $this->resetWords, true)) {
            $this->sendMenu($convo, $ctx);
            return;
        }

        match ($convo->step) {
            'lead_reason' => $this->captureReason($convo, $text, $ctx),
            default       => $this->routeMenu($convo, $input, $ctx),
        };
    }

    /** Router for the main menu and its sub-selections (step = idle). */
    private function routeMenu(WhatsAppConversation $c, string $input, array $ctx): void
    {
        if (str_starts_with($input, 'svc_')) {
            $this->serviceDetail($c, $input, $ctx);
            return;
        }
        if (str_starts_with($input, 'faq_')) {
            $this->faqAnswer($c, $input, $ctx);
            return;
        }

        switch ($input) {
            case 'menu_book':          $this->bookAppointment($c, $ctx); break;
            case 'menu_services':      $this->servicesList($c, $ctx); break;
            case 'menu_services_all':  $this->link($c, $ctx, "*Our Services* 🏥\n\nExplore all our departments and specialities here:", route('frontend.specialities')); break;
            case 'menu_team':          $this->teamInfo($c, $ctx); break;
            case 'menu_blog':          $this->link($c, $ctx, "*Blog & Articles* 📝\n\nExplore pet-care tips, updates and stories from our team here:", route('frontend.blogs')); break;
            case 'menu_timings':       $this->timings($c, $ctx); break;
            case 'menu_emergency':     $this->emergency($c, $ctx); break;
            case 'menu_faq':           $this->faqList($c, $ctx); break;
            case 'menu_talk':          $this->startTalk($c, $ctx); break;
            case 'menu_careers':       $this->link($c, $ctx, "*Careers at SAHM* 💼\n\nWe'd love to hear from you! View our current openings and apply here:", route('frontend.join_us')); break;
            default:                   $this->sendMenu($c, $ctx);   // unrecognised / "Main menu"
        }
    }

    /** Greeting + the main-menu list (warm, elegant tone). */
    private function sendMenu(WhatsAppConversation $c, array $ctx): void
    {
        $c->step = 'idle';
        $c->data = null;
        $c->save();

        $greeting = "Hello, and welcome to *Small Animal Hospital Mumbai*. 🐾\n\n"
            ."I'm here to help you and your pet. How may I assist you today?";

        $this->wa->sendList($c->wa_id, $greeting, 'Main Menu', [
            ['id' => 'menu_book',      'title' => 'Book an appointment', 'description' => 'Reserve a visit for your pet'],
            ['id' => 'menu_services',  'title' => 'Our services',        'description' => 'Departments & specialities'],
            ['id' => 'menu_team',      'title' => 'Meet the team',       'description' => 'Our doctors & specialists'],
            ['id' => 'menu_timings',   'title' => 'Timings & location',  'description' => 'Hours, address & directions'],
            ['id' => 'menu_emergency', 'title' => 'Emergency help',      'description' => 'Urgent care for your pet'],
            ['id' => 'menu_faq',       'title' => 'Common questions',    'description' => 'Fees, reports & more'],
            ['id' => 'menu_blog',      'title' => 'Blog & articles',     'description' => 'Pet-care tips & updates'],
            ['id' => 'menu_talk',      'title' => 'Talk to our team',    'description' => 'Speak with a real person'],
            ['id' => 'menu_careers',   'title' => 'Careers',             'description' => 'Join our team'],
        ], null, $ctx);
    }

    /** Book an appointment → OTP login link. */
    private function bookAppointment(WhatsAppConversation $c, array $ctx): void
    {
        $url = config('services.whatsapp.booking_url') ?: route('frontend.user_login');
        $this->wa->sendText($c->wa_id, "*Book an Appointment* 🐾\n\nWonderful — let's get your pet booked. Please tap below to log in and reserve your visit:\n{$url}\n\nOnce submitted, our Customer Care team will call you to confirm the details.", $ctx);
        $this->backToMenuHint($c, $ctx);
    }

    /** "Our services" — live list of specialities from the database. */
    private function servicesList(WhatsAppConversation $c, array $ctx): void
    {
        $items = Specialities::whereNull('deleted_by')->orderBy('id')->limit(9)->get();

        if ($items->isEmpty()) {
            $this->link($c, $ctx, "*Our Services* 🏥\n\nExplore our departments and specialities here:", route('frontend.specialities'));
            return;
        }

        $rows = $items->map(fn ($s) => ['id' => 'svc_'.$s->id, 'title' => $s->speciality])->values()->all();
        $rows[] = ['id' => 'menu_services_all', 'title' => 'View all on website'];

        $this->wa->sendList($c->wa_id, "*Our Services* 🏥\n\nHere's what we care for at SAHM. Tap any to learn more:", 'View Services', $rows, null, $ctx);
        $c->step = 'idle';
        $c->save();
    }

    /** One speciality → short intro + link to its website page. */
    private function serviceDetail(WhatsAppConversation $c, string $input, array $ctx): void
    {
        $id = (int) str_replace('svc_', '', $input);
        $s  = Specialities::whereNull('deleted_by')->find($id);

        if (! $s) {
            $this->sendMenu($c, $ctx);
            return;
        }

        $url = route('frontend.specialities_details', $s->slug);
        $this->wa->sendText($c->wa_id, "*{$s->speciality}*\n\nLearn more about our {$s->speciality} care here:\n{$url}\n\nWould you like to book an appointment or speak with our team?", $ctx);
        $this->wa->sendButtons($c->wa_id, 'Please choose an option:', [
            ['id' => 'menu_book', 'title' => 'Book appointment'],
            ['id' => 'menu_talk', 'title' => 'Talk to our team'],
            ['id' => 'menu_back', 'title' => 'Main menu'],
        ], null, $ctx);
        $c->step = 'idle';
        $c->save();
    }

    /** "Meet the team" — live team list from the database + link to the team page. */
    private function teamInfo(WhatsAppConversation $c, array $ctx): void
    {
        $members = OurTeam::whereNull('deleted_by')
            ->where('show_on_team_page', true)
            ->orderBy('name')
            ->limit(8)
            ->get();

        $url = route('frontend.our_team');

        if ($members->isEmpty()) {
            $this->wa->sendText($c->wa_id, "*Meet Our Team* 👩‍⚕️\n\nMeet our veterinarians and specialists here:\n{$url}", $ctx);
        } else {
            $lines = $members
                ->map(function ($m) {
                    $desig = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string) $m->designation))));
                    if (mb_strlen($desig) > 60) {
                        $desig = mb_substr($desig, 0, 57).'…';
                    }
                    return '• *'.$m->name.'*'.($desig !== '' ? ' — '.$desig : '');
                })
                ->implode("\n");
            $this->wa->sendText($c->wa_id, "*Meet Our Team* 👩‍⚕️\n\nOur experienced veterinarians and specialists:\n\n{$lines}\n\nView full profiles here:\n{$url}", $ctx);
        }

        $this->backToMenuHint($c, $ctx);
    }

    /** Timings & location. */
    private function timings(WhatsAppConversation $c, array $ctx): void
    {
        $this->wa->sendText($c->wa_id, "*Timings & Location* 📍\n\nSmall Animal Hospital Mumbai\n[Hospital address — to be confirmed]\n\n🕐 Mon–Sat: 9:00 AM – 8:00 PM\nSunday: 9:00 AM – 1:00 PM\n\n📞 022 6538 3538\n🗺️ Directions: [Google Maps link]", $ctx);
        $this->wa->sendButtons($c->wa_id, 'How would you like to proceed?', [
            ['id' => 'menu_book',      'title' => 'Book a visit'],
            ['id' => 'menu_emergency', 'title' => 'Emergency help'],
            ['id' => 'menu_back',      'title' => 'Main menu'],
        ], null, $ctx);
        $c->step = 'idle';
        $c->save();
    }

    /** Emergency — phone-first. */
    private function emergency(WhatsAppConversation $c, array $ctx): void
    {
        $this->wa->sendText($c->wa_id, "*Emergency Help* 🚨\n\nI'm here to help — a pet emergency is serious, so please don't wait.\n\n📞 *Call us now: 022 6538 3538*\n\n🏥 Or come straight to the hospital:\n[Hospital address — to be confirmed]\n\nIf you can, please bring any past reports or the medicine your pet is on.", $ctx);
        $this->wa->sendButtons($c->wa_id, 'We can also help with:', [
            ['id' => 'menu_timings', 'title' => 'Directions'],
            ['id' => 'menu_talk',    'title' => 'Message the team'],
            ['id' => 'menu_back',    'title' => 'Main menu'],
        ], null, $ctx);
        $c->step = 'idle';
        $c->save();
    }

    /** FAQ list. */
    private function faqList(WhatsAppConversation $c, array $ctx): void
    {
        $this->wa->sendList($c->wa_id, "*Common Questions* ❓\n\nPlease select a question:", 'View Questions',
            array_map(fn ($id) => ['id' => $id, 'title' => $this->faqs[$id][0], 'description' => $this->faqs[$id][1]], array_keys($this->faqs)), null, $ctx);
        $c->step = 'idle';
        $c->save();
    }

    /** One FAQ answer + follow-up buttons. */
    private function faqAnswer(WhatsAppConversation $c, string $id, array $ctx): void
    {
        $answer = $this->faqs[$id][2] ?? 'I am sorry, I could not find that answer.';
        $this->wa->sendText($c->wa_id, $answer."\n\nWas this helpful?", $ctx);
        $this->wa->sendButtons($c->wa_id, 'Please let me know:', [
            ['id' => 'menu_back', 'title' => 'Yes, thank you'],
            ['id' => 'menu_talk', 'title' => 'Talk to our team'],
        ], null, $ctx);
        $c->step = 'idle';
        $c->save();
    }

    /** "Talk to our team" — begin capturing the user's message. */
    private function startTalk(WhatsAppConversation $c, array $ctx): void
    {
        $c->step = 'lead_reason';
        $c->save();
        $this->wa->sendText($c->wa_id, "*Talk to Our Team* 💬\n\nOf course — I'll connect you with our Customer Care team. Please share your question below, and we'll get back to you shortly.", $ctx);
    }

    /** Save the captured message as a Contact Enquiry, then confirm. */
    private function captureReason(WhatsAppConversation $c, string $text, array $ctx): void
    {
        // Persist as a Contact Enquiry so it appears in the admin panel.
        // NOTE: WhatsApp gives us no email; contact_enquiries.email is NOT NULL,
        // so we store an empty placeholder. Revisit if a dedicated WhatsApp-leads
        // store (or a nullable email) is preferred.
        try {
            ContactEnquiry::create([
                'full_name' => $c->name ?: 'WhatsApp User',
                'email'     => '',
                'phone'     => $c->wa_id,
                'subject'   => 'WhatsApp Chatbot Enquiry',
                'message'   => trim($text),
            ]);
        } catch (\Throwable $e) {
            Log::error('WhatsApp lead save failed: '.$e->getMessage(), ['wa_id' => $c->wa_id]);
        }

        $c->step = 'idle';
        $c->save();

        $this->wa->sendText($c->wa_id, "Thank you! Your message has reached our Customer Care team, and they'll get back to you shortly.", $ctx);
        $this->backToMenuHint($c, $ctx);
    }

    /** Send a titled message with a link, then offer the menu. */
    private function link(WhatsAppConversation $c, array $ctx, string $intro, string $url): void
    {
        $this->wa->sendText($c->wa_id, $intro."\n".$url, $ctx);
        $this->backToMenuHint($c, $ctx);
    }

    /** Offer a quick way back to the menu after answering. */
    private function backToMenuHint(WhatsAppConversation $c, array $ctx): void
    {
        $c->step = 'idle';
        $c->save();
        $this->wa->sendButtons($c->wa_id, 'Is there anything else I can help you with?', [
            ['id' => 'menu_back', 'title' => 'Main menu'],
        ], null, $ctx);
    }
}
