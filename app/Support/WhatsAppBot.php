<?php

namespace App\Support;

use App\Models\ContactDetails;
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
 * Warm, elegant tone. Services & Team are pulled LIVE from the database; address,
 * emergency number and map link come from ContactDetails. Website links are sent
 * as tidy CTA-URL buttons rather than raw links.
 *
 * "Book an appointment" sends the OTP login link (per instruction).
 */
class WhatsAppBot
{
    public function __construct(private WhatsAppFortius $wa) {}

    /** Words that always restart the conversation at the main menu. */
    private array $resetWords = ['hi', 'hello', 'hey', 'menu', 'start', 'main menu', 'restart'];

    private ?ContactDetails $contact = null;
    private bool $contactLoaded = false;

    /** FAQs shown under "Common questions". [short title, full question, answer]. */
    private array $faqs = [
        'faq_fees'    => ['💳 Consultation fees', 'What are the consultation fees?', "Consultation fees vary by service. Our team will happily share the exact charges when they call you. For immediate help, please call 022-6538-3538."],
        'faq_reports' => ['📄 Medical reports',   "Can I get my pet's reports?",     "Absolutely — your pet's reports can be collected at the hospital or shared with you digitally. Our reception team will be glad to help."],
        'faq_bring'   => ['🧾 What to bring',     'What should I bring for a visit?', "Great question! Please carry:\n• Your pet's previous prescriptions or reports\n• Vaccination card, if any\n• A leash or carrier for safe travel"],
        'faq_parking' => ['🅿️ Parking',           'Is parking available?', "Parking details are being updated. [to be confirmed]"],
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
            case 'menu_services_all':  $this->wa->sendCtaUrl($c->wa_id, "*Our Services* 🏥\n\nExplore all our departments and specialities on our website.", 'View Services', route('frontend.specialities'), $ctx); $this->backToMenuHint($c, $ctx); break;
            case 'menu_team':          $this->teamInfo($c, $ctx); break;
            case 'menu_blog':          $this->wa->sendCtaUrl($c->wa_id, "*Blog & Articles* 📝\n\nExplore pet-care tips, heart-warming stories and updates from our team.", 'Read Our Blog', route('frontend.blogs'), $ctx); $this->backToMenuHint($c, $ctx); break;
            case 'menu_timings':       $this->timings($c, $ctx); break;
            case 'menu_emergency':     $this->emergency($c, $ctx); break;
            case 'menu_faq':           $this->faqList($c, $ctx); break;
            case 'menu_talk':          $this->startTalk($c, $ctx); break;
            default:                   $this->sendMenu($c, $ctx);   // unrecognised / "Main menu"
        }
    }

    /** Greeting (warm) + branded favicon image + the main-menu list. Shown every time the menu is requested. */
    private function sendMenu(WhatsAppConversation $c, array $ctx): void
    {
        $c->step = 'idle';
        $c->data = null;
        $c->save();

        $greeting = "Hello, and a warm welcome to *Small Animal Hospital Mumbai*! 🐾\n\n"
            ."We're delighted to have you here. Your pet's health and happiness mean the world to us, and I'm here to help you every step of the way.\n\n"
            ."How may I assist you and your furry friend today?";

        // Branded welcome image (public JPG/PNG — favicon by default), shown with every menu.
        $image = config('services.whatsapp.welcome_image') ?: asset('frontend/assets/img/favicon.png');
        if ($image) {
            $this->wa->sendImage($c->wa_id, $image, $greeting, $ctx);
            $body = 'Please choose an option below:';
        } else {
            $body = $greeting;
        }

        $this->wa->sendList($c->wa_id, $body, 'Main Menu', [
            ['id' => 'menu_book',      'title' => '📅 Book appointment', 'description' => 'Reserve a visit for your pet'],
            ['id' => 'menu_services',  'title' => '🏥 Our services',     'description' => 'Departments & specialities'],
            ['id' => 'menu_team',      'title' => '🩺 Meet the team',    'description' => 'Our doctors & specialists'],
            ['id' => 'menu_timings',   'title' => '📍 Timings & location', 'description' => 'Hours, address & directions'],
            ['id' => 'menu_emergency', 'title' => '🚨 Emergency help',   'description' => 'Urgent care for your pet'],
            ['id' => 'menu_faq',       'title' => '❓ Common questions',  'description' => 'Fees, reports & more'],
            ['id' => 'menu_blog',      'title' => '📝 Blog & articles',  'description' => 'Pet-care tips & updates'],
            ['id' => 'menu_talk',      'title' => '💬 Talk to our team', 'description' => 'Speak with a real person'],
        ], null, $ctx);
    }

    /** Book an appointment → OTP login link, as a tidy CTA button. */
    private function bookAppointment(WhatsAppConversation $c, array $ctx): void
    {
        $url  = config('services.whatsapp.booking_url') ?: route('frontend.user_login');
        $body = "*Book an Appointment* 🐾\n\nWonderful — let's get your pet booked! Tap below to log in and reserve your visit. Our Customer Care team will then call you to confirm the details.";
        $this->wa->sendCtaUrl($c->wa_id, $body, 'Book Now', $url, $ctx);
        $this->backToMenuHint($c, $ctx);
    }

    /** "Our services" — live list of specialities from the database. */
    private function servicesList(WhatsAppConversation $c, array $ctx): void
    {
        $items = Specialities::whereNull('deleted_by')->orderBy('id')->limit(9)->get();

        if ($items->isEmpty()) {
            $this->wa->sendCtaUrl($c->wa_id, "*Our Services* 🏥\n\nExplore our departments and specialities on our website.", 'View Services', route('frontend.specialities'), $ctx);
            $this->backToMenuHint($c, $ctx);
            return;
        }

        $rows = $items->map(fn ($s) => ['id' => 'svc_'.$s->id, 'title' => $s->speciality])->values()->all();
        $rows[] = ['id' => 'menu_services_all', 'title' => '🔎 View all on website'];

        $this->wa->sendList($c->wa_id, "*Our Services* 🏥\n\nHere's what we care for at SAHM. Tap any to learn more:", 'View Services', $rows, null, $ctx);
        $c->step = 'idle';
        $c->save();
    }

    /** One speciality → short intro + CTA button to its website page + next-step buttons. */
    private function serviceDetail(WhatsAppConversation $c, string $input, array $ctx): void
    {
        $id = (int) str_replace('svc_', '', $input);
        $s  = Specialities::whereNull('deleted_by')->find($id);

        if (! $s) {
            $this->sendMenu($c, $ctx);
            return;
        }

        $url = route('frontend.specialities_details', $s->slug);
        $this->wa->sendCtaUrl($c->wa_id, "*{$s->speciality}* 🐾\n\nTap below to learn more about our {$s->speciality} care.", 'Learn More', $url, $ctx);
        $this->wa->sendButtons($c->wa_id, 'Or choose an option:', [
            ['id' => 'menu_book', 'title' => '📅 Book appointment'],
            ['id' => 'menu_talk', 'title' => '💬 Talk to our team'],
            ['id' => 'menu_back', 'title' => '🏠 Main menu'],
        ], null, $ctx);
        $c->step = 'idle';
        $c->save();
    }

    /** "Meet the team" — live team list + CTA button to the team page. */
    private function teamInfo(WhatsAppConversation $c, array $ctx): void
    {
        $members = OurTeam::whereNull('deleted_by')
            ->where('show_on_team_page', true)
            ->orderBy('name')
            ->limit(8)
            ->get();

        if ($members->isEmpty()) {
            $this->wa->sendCtaUrl($c->wa_id, "*Meet Our Team* 🩺\n\nMeet our wonderful veterinarians and specialists.", 'View Profiles', route('frontend.our_team'), $ctx);
            $this->backToMenuHint($c, $ctx);
            return;
        }

        $lines = $members
            ->map(function ($m) {
                $desig = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string) $m->designation))));
                if (mb_strlen($desig) > 60) {
                    $desig = mb_substr($desig, 0, 57).'…';
                }
                return '• *'.$m->name.'*'.($desig !== '' ? ' — '.$desig : '');
            })
            ->implode("\n");

        $this->wa->sendCtaUrl($c->wa_id, "*Meet Our Team* 🩺\n\nOur experienced veterinarians and specialists:\n\n{$lines}", 'View Full Profiles', route('frontend.our_team'), $ctx);
        $this->backToMenuHint($c, $ctx);
    }

    /** Timings & location — pulled from ContactDetails, with a Directions button. */
    private function timings(WhatsAppConversation $c, array $ctx): void
    {
        $body = "*Timings & Location* 📍\n\n"
            .$this->address()."\n\n"
            ."🕐 *Working Hours*\nMon–Sat: 9:00 AM – 8:00 PM\nSunday: 9:00 AM – 1:00 PM\n\n"
            ."📞 ".$this->emergencyNo();

        if ($map = $this->mapUrl()) {
            $this->wa->sendCtaUrl($c->wa_id, $body, 'Get Directions', $map, $ctx);
        } else {
            $this->wa->sendText($c->wa_id, $body, $ctx);
        }
        $this->backToMenuHint($c, $ctx);
    }

    /** Emergency — phone-first, with a Directions button. */
    private function emergency(WhatsAppConversation $c, array $ctx): void
    {
        $no   = $this->emergencyNo();
        $body = "*Emergency Help* 🚨\n\nYour pet's wellbeing can't wait — we're here for you. Please contact us right away.\n\n"
            ."📞 Call now: *{$no}*\n\n"
            ."🏥 Or come straight to the hospital:\n".$this->address()."\n\n"
            ."If you can, please bring any past reports or the medicine your pet is on.";

        if ($map = $this->mapUrl()) {
            $this->wa->sendCtaUrl($c->wa_id, $body, 'Get Directions', $map, $ctx);
        } else {
            $this->wa->sendText($c->wa_id, $body, $ctx);
        }
        $this->backToMenuHint($c, $ctx);
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
            ['id' => 'menu_back', 'title' => '👍 Yes, thank you'],
            ['id' => 'menu_talk', 'title' => '💬 Talk to our team'],
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

    /** Offer a quick way back to the menu after answering. */
    private function backToMenuHint(WhatsAppConversation $c, array $ctx): void
    {
        $c->step = 'idle';
        $c->save();
        $this->wa->sendButtons($c->wa_id, 'Is there anything else I can help you with?', [
            ['id' => 'menu_back', 'title' => '🏠 Main menu'],
        ], null, $ctx);
    }

    /* -------------------------------------------------------------------- */
    /* Contact details (address / emergency no / map) pulled from the DB    */
    /* -------------------------------------------------------------------- */

    private function contact(): ?ContactDetails
    {
        if (! $this->contactLoaded) {
            $this->contact = ContactDetails::whereNull('deleted_by')->first();
            $this->contactLoaded = true;
        }
        return $this->contact;
    }

    private function address(): string
    {
        $a = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string) $this->contact()?->address))));
        $a = trim(preg_replace('/\s*,\s*/', ', ', $a)); // tidy comma spacing
        return $a !== '' ? $a : '[Hospital address — to be confirmed]';
    }

    private function emergencyNo(): string
    {
        return $this->contact()?->emergency_no ?: '022-6538-3538';
    }

    private function mapUrl(): ?string
    {
        $m = $this->contact()?->map_url;
        return $m ?: null;
    }
}
