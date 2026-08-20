<?php

namespace App\Support;

use App\Models\ContactEnquiry;
use App\Models\WhatsAppConversation;
use App\Services\WhatsAppFortius;
use Illuminate\Support\Facades\Log;

/**
 * Reactive WhatsApp chatbot flow for Small Animal Hospital Mumbai.
 * Runs inside the 24-hour window (user messages first → we reply free-form),
 * so NO approved templates are needed.
 *
 * Menu + wording follow the client-approved concept (7 flows). Content marked
 * "(placeholder)" needs the real hospital data (address, timings, fees, links).
 *
 * NOTE: Per the latest instruction, "Book an appointment" sends the OTP login
 * link rather than the full in-chat guided booking shown in the concept.
 */
class WhatsAppBot
{
    public function __construct(private WhatsAppFortius $wa) {}

    /** Words that always restart the conversation at the main menu. */
    private array $resetWords = ['hi', 'hello', 'hey', 'menu', 'start', 'main menu', 'restart'];

    /** Services shown under "Our services". Descriptions are placeholders — confirm / wire to Specialities. */
    private array $services = [
        'svc_general'     => ['General Medicine',        'General consultation and treatment for everyday illnesses and routine check-ups.'],
        'svc_surgery'     => ['Surgery',                 'Routine and advanced surgical procedures in a fully-equipped theatre.'],
        'svc_vaccination' => ['Vaccination & Prev. Care', 'Core vaccines, deworming and wellness check-ups tailored to your pet.'],
        'svc_ortho'       => ['Orthopaedics',            "Bone, joint and fracture care for your pet's mobility."],
        'svc_diagnostics' => ['Diagnostics & Imaging',   'In-house lab, X-ray and imaging for quick, accurate diagnosis.'],
    ];

    /** FAQs shown under "Common questions". [short title, full question, answer]. */
    private array $faqs = [
        'faq_fees'    => ['Consultation fees', 'What are the consultation fees?', "Fees vary by service. _(placeholder — confirm fees)_\nOur team will share exact charges on your confirmation call."],
        'faq_reports' => ['Pet reports',       "Can I get my pet's reports?",     'Yes — reports can be collected at the hospital or shared digitally. _(placeholder — confirm process)_'],
        'faq_bring'   => ['What to bring',     'What should I bring for a visit?', "Please carry:\n• Previous prescriptions or reports 📄\n• Vaccination card, if any 💳\n• A leash or carrier 🦮"],
        'faq_parking' => ['Parking',           'Is parking available?',            '_(placeholder — confirm parking availability)_'],
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
            case 'menu_book':
                $url = config('services.whatsapp.booking_url');
                $this->wa->sendText($c->wa_id, "📅 *Book an Appointment*\nWonderful — let's get your pet booked. 🐾\n\nTap below to log in and request your visit:\n{$url}\n\nOur Customer Care team will call to confirm the exact time.", $ctx);
                $this->backToMenuHint($c, $ctx);
                break;

            case 'menu_services':
                $this->wa->sendList($c->wa_id, "Here's what we care for at SAHM. Tap any to know more 👇", 'View Services',
                    array_map(fn ($id) => ['id' => $id, 'title' => $this->services[$id][0]], array_keys($this->services)), null, $ctx);
                $c->step = 'idle';
                $c->save();
                break;

            case 'menu_timings':
                $this->wa->sendText($c->wa_id, "📍 *Timings & Location*\n\n🏥 Small Animal Hospital Mumbai\n[ Hospital address — Mumbai ]  _(placeholder)_\n\n🕐 Mon–Sat · 9:00 AM – 8:00 PM\nSunday · 9:00 AM – 1:00 PM\n_(placeholder — confirm timings)_\n\n📞 022 6538 3538\n🗺️ Directions: [ Google Maps link ]", $ctx);
                $this->wa->sendButtons($c->wa_id, 'What next?', [
                    ['id' => 'menu_book',      'title' => '📅 Book a visit'],
                    ['id' => 'menu_emergency', 'title' => '🚨 Emergency'],
                    ['id' => 'menu_back',      'title' => '↩️ Main Menu'],
                ], null, $ctx);
                $c->step = 'idle';
                $c->save();
                break;

            case 'menu_emergency':
                $this->wa->sendText($c->wa_id, "🚨 *Emergency Help*\nA pet emergency is serious — please don't wait. 🐾\n\n☎️ *Call us now: 022 6538 3538*\n\n🏥 Or come straight to the hospital:\n[ Hospital address ]  _(placeholder)_\n\nIf you can, bring any past reports or the medicine your pet is on.", $ctx);
                $this->wa->sendButtons($c->wa_id, 'We can also help with:', [
                    ['id' => 'menu_timings', 'title' => '🗺️ Directions'],
                    ['id' => 'menu_talk',    'title' => '💬 Message team'],
                    ['id' => 'menu_back',    'title' => '↩️ Main Menu'],
                ], null, $ctx);
                $c->step = 'idle';
                $c->save();
                break;

            case 'menu_faq':
                $this->wa->sendList($c->wa_id, 'Tap a question to see the answer 👇', 'View Questions',
                    array_map(fn ($id) => ['id' => $id, 'title' => $this->faqs[$id][0], 'description' => $this->faqs[$id][1]], array_keys($this->faqs)), null, $ctx);
                $c->step = 'idle';
                $c->save();
                break;

            case 'menu_talk':
                $c->step = 'lead_reason';
                $c->save();
                $this->wa->sendText($c->wa_id, "💬 Of course! Connecting you with our Customer Care team. 🧑‍⚕️\n\nPlease share your question below and we'll get back to you shortly.", $ctx);
                break;

            case 'menu_careers':
                $this->wa->sendText($c->wa_id, "💼 *Careers at SAHM*\nWe'd love to hear from you! View current openings and apply here:\n".route('frontend.join_us'), $ctx);
                $this->backToMenuHint($c, $ctx);
                break;

            default:
                // Unrecognised input (incl. the "Main Menu" button) — greet + show menu.
                $this->sendMenu($c, $ctx);
        }
    }

    /** Greeting + the approved main-menu list. */
    private function sendMenu(WhatsAppConversation $c, array $ctx): void
    {
        $c->step = 'idle';
        $c->data = null;
        $c->save();

        $greeting = "🐾 Hello and welcome to *Small Animal Hospital Mumbai*!\n\n"
            .'I'."'m the hospital's assistant — here for you and your furry family. 🐶🐱\n\nHow can I help you today?";

        $this->wa->sendList($c->wa_id, $greeting, 'Main Menu', [
            ['id' => 'menu_book',      'title' => 'Book an appointment', 'description' => 'Reserve a visit for your pet'],
            ['id' => 'menu_services',  'title' => 'Our services',        'description' => 'Departments & specialities'],
            ['id' => 'menu_timings',   'title' => 'Timings & location',  'description' => 'Hours, address & directions'],
            ['id' => 'menu_emergency', 'title' => 'Emergency help',      'description' => 'Urgent — my pet needs care now'],
            ['id' => 'menu_faq',       'title' => 'Common questions',    'description' => 'Fees, reports, what to bring'],
            ['id' => 'menu_talk',      'title' => 'Talk to our team',    'description' => 'Chat with a real person'],
            ['id' => 'menu_careers',   'title' => 'Careers',             'description' => 'Work with us'],
        ], null, $ctx);
    }

    /** One service's description + next-step buttons. */
    private function serviceDetail(WhatsAppConversation $c, string $id, array $ctx): void
    {
        [$title, $desc] = $this->services[$id] ?? ['Our Services', ''];
        $this->wa->sendText($c->wa_id, "*{$title}*\n\n{$desc}\n\nWould you like to book, or ask our team a question?", $ctx);
        $this->wa->sendButtons($c->wa_id, 'Choose an option:', [
            ['id' => 'menu_book', 'title' => '📅 Book this'],
            ['id' => 'menu_talk', 'title' => '💬 Ask the team'],
            ['id' => 'menu_back', 'title' => '↩️ Main menu'],
        ], null, $ctx);
        $c->step = 'idle';
        $c->save();
    }

    /** One FAQ answer + follow-up buttons. */
    private function faqAnswer(WhatsAppConversation $c, string $id, array $ctx): void
    {
        $answer = $this->faqs[$id][2] ?? 'Sorry, I could not find that answer.';
        $this->wa->sendText($c->wa_id, $answer."\n\nWas this helpful?", $ctx);
        $this->wa->sendButtons($c->wa_id, ' ', [
            ['id' => 'menu_back', 'title' => '👍 Yes, thanks'],
            ['id' => 'menu_talk', 'title' => '💬 Talk to team'],
        ], null, $ctx);
        $c->step = 'idle';
        $c->save();
    }

    /** Offer a quick way back to the menu after answering. */
    private function backToMenuHint(WhatsAppConversation $c, array $ctx): void
    {
        $c->step = 'idle';
        $c->save();
        $this->wa->sendButtons($c->wa_id, 'Is there anything else we can help with?', [
            ['id' => 'menu_back', 'title' => '↩️ Main Menu'],
        ], null, $ctx);
    }

    /** "Talk to our team" / "Ask the team" — capture the question as a Contact Enquiry. */
    private function captureReason(WhatsAppConversation $c, string $text, array $ctx): void
    {
        // Persist as a Contact Enquiry so it appears in the admin panel.
        // NOTE: WhatsApp gives us no email, but contact_enquiries.email is NOT NULL,
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

        $this->wa->sendText($c->wa_id, '🙏 Thank you! Our Customer Care team has received your message and will get back to you shortly.', $ctx);
        $this->backToMenuHint($c, $ctx);
    }
}
