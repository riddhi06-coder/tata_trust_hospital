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
 * Reactive WhatsApp chatbot for Small Animal Hospital Mumbai.
 * Runs inside the 24-hour window (free-form, no templates needed).
 *
 * INLINE numbered-menu style: options are shown directly in the chat and the
 * user replies with a number (no pop-out list). Services & Team come live from
 * the DB; address / emergency no / map from ContactDetails. Links are sent as
 * text with a preview card (Fortius does not support interactive URL buttons).
 */
class WhatsAppBot
{
    public function __construct(private WhatsAppFortius $wa) {}

    /** Words / inputs that always return to the main menu. */
    private array $resetWords = ['hi', 'hello', 'hey', 'menu', 'start', 'main menu', 'restart', '0'];

    /** Number → circled-number emoji for a tidy inline list. */
    private array $num = [1 => '1️⃣', 2 => '2️⃣', 3 => '3️⃣', 4 => '4️⃣', 5 => '5️⃣', 6 => '6️⃣', 7 => '7️⃣', 8 => '8️⃣', 9 => '9️⃣', 10 => '🔟'];

    /** Main menu: number => [action, label]. */
    private array $mainMenu = [
        1 => ['book',      '📅 Book an appointment'],
        2 => ['services',  '🏥 Our services'],
        3 => ['team',      '🩺 Meet the team'],
        4 => ['timings',   '📍 Timings & location'],
        5 => ['emergency', '🚨 Emergency help'],
        6 => ['faq',       '❓ Common questions'],
        7 => ['blog',      '📝 Blog & articles'],
        8 => ['talk',      '💬 Talk to our team'],
    ];

    /** FAQs (order preserved). [title, question, answer]. */
    private array $faqs = [
        'fees'    => ['💳 Consultation fees', 'What are the consultation fees?', "Consultation fees vary by service. Our team will happily share the exact charges when they call you. For immediate help, please call 022-6538-3538."],
        'reports' => ['📄 Medical reports',   "Can I get my pet's reports?",     "Absolutely — your pet's reports can be collected at the hospital or shared with you digitally. Our reception team will be glad to help."],
        'bring'   => ['🧾 What to bring',     'What should I bring for a visit?', "Great question! Please carry:\n• Your pet's previous prescriptions or reports\n• Vaccination card, if any\n• A leash or carrier for safe travel"],
        'parking' => ['🅿️ Parking',           'Is parking available?', "Parking details are being updated. [to be confirmed]"],
    ];

    private ?ContactDetails $contact = null;
    private bool $contactLoaded = false;

    public function handle(string $waId, ?string $profileName, string $text, ?string $interactiveId = null): void
    {
        $convo = WhatsAppConversation::firstOrNew(['wa_id' => $waId]);
        if ($profileName && ! $convo->name) {
            $convo->name = $profileName;
        }
        $convo->last_message_at = now();

        // Rolling activity history in the data column (capped at 25).
        $data = $convo->data ?? [];
        $data['history'] = array_slice(array_merge($data['history'] ?? [], [[
            'at' => now()->toDateTimeString(),
            'in' => mb_substr((string) ($interactiveId ?: $text), 0, 200),
        ]]), -25);
        $convo->data = $data;
        $convo->save();

        $raw   = trim((string) ($interactiveId ?: $text));
        $lower = strtolower($raw);
        $ctx   = ['recipient_name' => $convo->name];

        // Always allow jumping back to the main menu.
        if (in_array($lower, $this->resetWords, true)) {
            $this->mainMenu($convo, $ctx);
            return;
        }

        // Free-text capture (Talk to our team).
        if ($convo->step === 'lead_reason') {
            $this->captureReason($convo, $text, $ctx);
            return;
        }

        $n = ctype_digit($lower) ? (int) $lower : null;

        if ($convo->step === 'menu_services' && $n !== null) {
            $this->pickService($convo, $n, $ctx);
            return;
        }
        if ($convo->step === 'menu_faq' && $n !== null) {
            $this->pickFaq($convo, $n, $ctx);
            return;
        }
        if ($n !== null) {
            $this->pickMain($convo, $n, $ctx);
            return;
        }

        // Anything unrecognised → show the menu.
        $this->mainMenu($convo, $ctx);
    }

    /** Route a main-menu number to its action. */
    private function pickMain(WhatsAppConversation $c, int $n, array $ctx): void
    {
        $item = $this->mainMenu[$n] ?? null;
        if (! $item) {
            $this->mainMenu($c, $ctx);
            return;
        }

        match ($item[0]) {
            'book'      => $this->bookAppointment($c, $ctx),
            'services'  => $this->servicesList($c, $ctx),
            'team'      => $this->teamInfo($c, $ctx),
            'timings'   => $this->timings($c, $ctx),
            'emergency' => $this->emergency($c, $ctx),
            'faq'       => $this->faqList($c, $ctx),
            'blog'      => $this->blog($c, $ctx),
            'talk'      => $this->startTalk($c, $ctx),
            default     => $this->mainMenu($c, $ctx),
        };
    }

    /** Branded welcome image + warm greeting + the inline numbered main menu. */
    private function mainMenu(WhatsAppConversation $c, array $ctx): void
    {
        $c->step = 'menu_main';
        $c->save();

        $greeting = "Hello, and a warm welcome to *Small Animal Hospital Mumbai*! 🐾🐶🐱\n\n"
            ."We're so happy to have you and your companion here. Your pet's health and happiness mean the world to us. 🐾";

        $image = config('services.whatsapp.welcome_image') ?: asset('frontend/assets/img/logo/tata-trust-logo.png');
        if ($image) {
            $this->wa->sendImage($c->wa_id, $image, $greeting, $ctx);
            $lead = "How may I help you today?";
        } else {
            $lead = $greeting."\n\nHow may I help you today?";
        }

        $lines = '';
        foreach ($this->mainMenu as $i => $item) {
            $lines .= $this->numFor($i).'  '.$item[1]."\n";
        }

        $this->wa->sendText($c->wa_id, $lead."\n\n".$lines."\n_Reply with a number (1-8)_ 🐾", $ctx);
    }

    /** Book an appointment → OTP login link. */
    private function bookAppointment(WhatsAppConversation $c, array $ctx): void
    {
        $c->step = 'menu_main';
        $c->save();
        $url = config('services.whatsapp.booking_url') ?: route('frontend.user_login');
        $this->wa->sendText($c->wa_id, "*Book an Appointment* 🐾\n\nWonderful — let's get your pet booked! Tap the link below to log in and reserve your visit. Our Customer Care team will then call you to confirm the details.\n".$url.$this->backHint(), $ctx);
    }

    /** "Our services" — inline numbered list of specialities from the DB. */
    private function servicesList(WhatsAppConversation $c, array $ctx): void
    {
        $items = Specialities::whereNull('deleted_by')->orderBy('id')->limit(9)->get();

        if ($items->isEmpty()) {
            $c->step = 'menu_main';
            $c->save();
            $this->wa->sendText($c->wa_id, "*Our Services* 🏥\n\nExplore our departments and specialities:\n".route('frontend.specialities').$this->backHint(), $ctx);
            return;
        }

        $ids = [];
        $lines = '';
        $i = 1;
        foreach ($items as $s) {
            $ids[$i] = $s->id;
            $lines .= $this->numFor($i).'  '.$s->speciality."\n";
            $i++;
        }
        $allNum = $i;
        $lines .= $this->numFor($allNum).'  🔎 View all on website'."\n";

        $data = $c->data ?? [];
        $data['services']     = $ids;
        $data['services_all'] = $allNum;
        $c->data = $data;
        $c->step = 'menu_services';
        $c->save();

        $this->wa->sendText($c->wa_id, "*Our Services* 🏥\n\nHere's what we care for at SAHM:\n\n".$lines."\n_Reply with a number, or *menu* to go back._", $ctx);
    }

    /** Handle a service-number reply. */
    private function pickService(WhatsAppConversation $c, int $n, array $ctx): void
    {
        $data = $c->data ?? [];

        if ($n === ($data['services_all'] ?? -1)) {
            $c->step = 'menu_main';
            $c->save();
            $this->wa->sendText($c->wa_id, "*Our Services* 🏥\n\nExplore all our departments and specialities:\n".route('frontend.specialities').$this->backHint(), $ctx);
            return;
        }

        $id = $data['services'][$n] ?? null;
        $s  = $id ? Specialities::whereNull('deleted_by')->find($id) : null;
        if (! $s) {
            $this->servicesList($c, $ctx);
            return;
        }

        $c->step = 'menu_main';
        $c->save();
        $this->wa->sendText($c->wa_id, "*{$s->speciality}* 🐾\n\nLearn all about our {$s->speciality} care here:\n".route('frontend.specialities_details', $s->slug).$this->backHint(), $ctx);
    }

    /** "Meet the team" — live team list + link. */
    private function teamInfo(WhatsAppConversation $c, array $ctx): void
    {
        $c->step = 'menu_main';
        $c->save();

        $members = OurTeam::whereNull('deleted_by')
            ->where('show_on_team_page', true)
            ->orderBy('name')
            ->limit(8)
            ->get();

        $url = route('frontend.our_team');

        if ($members->isEmpty()) {
            $this->wa->sendText($c->wa_id, "*Meet Our Team* 🩺\n\nMeet our wonderful veterinarians and specialists:\n".$url.$this->backHint(), $ctx);
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

        $this->wa->sendText($c->wa_id, "*Meet Our Team* 🩺\n\nOur experienced veterinarians and specialists:\n\n".$lines."\n\nView full profiles:\n".$url.$this->backHint(), $ctx);
    }

    /** Blog & articles → link. */
    private function blog(WhatsAppConversation $c, array $ctx): void
    {
        $c->step = 'menu_main';
        $c->save();
        $this->wa->sendText($c->wa_id, "*Blog & Articles* 📝\n\nExplore pet-care tips, heart-warming stories and updates from our team:\n".route('frontend.blogs').$this->backHint(), $ctx);
    }

    /** Timings & location — from ContactDetails, with a directions link. */
    private function timings(WhatsAppConversation $c, array $ctx): void
    {
        $c->step = 'menu_main';
        $c->save();

        $body = "*Timings & Location* 📍\n\n"
            .$this->address()."\n\n"
            ."🕐 *Working Hours*\nMon–Sat: 9:00 AM – 8:00 PM\nSunday: 9:00 AM – 1:00 PM\n\n"
            ."📞 ".$this->emergencyNo();

        if ($map = $this->mapUrl()) {
            $body .= "\n\n🗺️ Get directions:\n".$map;
        }

        $this->wa->sendText($c->wa_id, $body.$this->backHint(), $ctx);
    }

    /** Emergency — phone-first, with a directions link. */
    private function emergency(WhatsAppConversation $c, array $ctx): void
    {
        $c->step = 'menu_main';
        $c->save();

        $body = "*Emergency Help* 🚨\n\nYour pet's wellbeing can't wait — we're here for you. Please contact us right away.\n\n"
            ."📞 Call now: *".$this->emergencyNo()."*\n\n"
            ."🏥 Or come straight to the hospital:\n".$this->address();

        if ($map = $this->mapUrl()) {
            $body .= "\n\n🗺️ Get directions:\n".$map;
        }

        $body .= "\n\nIf you can, please bring any past reports or the medicine your pet is on.";

        $this->wa->sendText($c->wa_id, $body.$this->backHint(), $ctx);
    }

    /** "Common questions" — inline numbered FAQ list. */
    private function faqList(WhatsAppConversation $c, array $ctx): void
    {
        $c->step = 'menu_faq';
        $c->save();

        $lines = '';
        $i = 1;
        foreach ($this->faqs as $faq) {
            $lines .= $this->numFor($i).'  '.$faq[0]."\n";
            $i++;
        }

        $this->wa->sendText($c->wa_id, "*Common Questions* ❓\n\n".$lines."\n_Reply with a number, or *menu* to go back._", $ctx);
    }

    /** Handle an FAQ-number reply. */
    private function pickFaq(WhatsAppConversation $c, int $n, array $ctx): void
    {
        $key = array_keys($this->faqs)[$n - 1] ?? null;
        if (! $key) {
            $this->faqList($c, $ctx);
            return;
        }

        $c->step = 'menu_main';
        $c->save();
        $this->wa->sendText($c->wa_id, $this->faqs[$key][2].$this->backHint(), $ctx);
    }

    /** "Talk to our team" — begin capturing the user's message. */
    private function startTalk(WhatsAppConversation $c, array $ctx): void
    {
        $c->step = 'lead_reason';
        $c->save();
        $this->wa->sendText($c->wa_id, "*Talk to Our Team* 💬\n\nOf course — I'll connect you with our Customer Care team. Please type your question or message below, and we'll get back to you shortly.", $ctx);
    }

    /** Save the captured message as a Contact Enquiry, then confirm. */
    private function captureReason(WhatsAppConversation $c, string $text, array $ctx): void
    {
        // Persist as a Contact Enquiry so it appears in the admin panel.
        // NOTE: WhatsApp gives us no email; contact_enquiries.email is NOT NULL,
        // so we store an empty placeholder.
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

        $data = $c->data ?? [];
        $data['enquiries'][] = ['at' => now()->toDateTimeString(), 'message' => trim($text)];
        $c->data = $data;
        $c->step = 'menu_main';
        $c->save();

        $this->wa->sendText($c->wa_id, "Thank you! Your message has reached our Customer Care team, and they'll get back to you shortly.".$this->backHint(), $ctx);
    }

    /* --------------------------------------------------------------------- */

    private function numFor(int $i): string
    {
        return $this->num[$i] ?? $i.'.';
    }

    private function backHint(): string
    {
        return "\n\n↩️ _Reply *menu* to see all options._";
    }

    /* ---- Contact details (address / emergency no / map) from the DB ------ */

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
        $a = trim(preg_replace('/\s*,\s*/', ', ', $a));
        return $a !== '' ? $a : '[Hospital address — to be confirmed]';
    }

    private function emergencyNo(): string
    {
        return $this->contact()?->emergency_no ?: '022-6538-3538';
    }

    private function mapUrl(): ?string
    {
        return $this->contact()?->map_url ?: null;
    }
}
