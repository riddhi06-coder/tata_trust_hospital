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
 * Flows follow the client-approved concept; tone is professional & polished.
 * Content marked "[to be confirmed]" needs the real hospital data.
 *
 * NOTE: "Book an appointment" sends the OTP login link (per instruction),
 * rather than the full in-chat guided booking shown in the concept.
 */
class WhatsAppBot
{
    public function __construct(private WhatsAppFortius $wa) {}

    /** Words that always restart the conversation at the main menu. */
    private array $resetWords = ['hi', 'hello', 'hey', 'menu', 'start', 'main menu', 'restart'];

    /** Services shown under "Our services". Descriptions are placeholders — confirm / wire to Specialities. */
    private array $services = [
        'svc_general'     => ['General Medicine',              'Consultations and treatment for routine and complex health concerns.'],
        'svc_surgery'     => ['Surgery',                       'Routine and advanced surgical procedures in a fully equipped operating theatre.'],
        'svc_vaccination' => ['Vaccination & Preventive Care', 'Core vaccinations, deworming and wellness check-ups tailored to your pet.'],
        'svc_ortho'       => ['Orthopaedics',                  'Diagnosis and treatment of bone, joint and mobility conditions.'],
        'svc_diagnostics' => ['Diagnostics & Imaging',         'In-house laboratory, X-ray and imaging for accurate diagnosis.'],
    ];

    /** FAQs shown under "Common questions". [short title, full question, answer]. */
    private array $faqs = [
        'faq_fees'    => ['Consultation fees', 'What are the consultation fees?', "Consultation fees vary by service and department. Our team will confirm the exact charges when they contact you. For immediate assistance, please call 022 6538 3538."],
        'faq_reports' => ['Medical reports',   "Can I access my pet's medical reports?", "Yes. Reports can be collected at the hospital or shared with you digitally. Our reception team will be glad to assist you."],
        'faq_bring'   => ['What to bring',     'What should I bring for a visit?', "Please carry:\n• Your pet's previous prescriptions or reports\n• Vaccination card, if available\n• A leash or carrier for safe transport"],
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
            case 'menu_book':
                $url = config('services.whatsapp.booking_url');
                $this->wa->sendText($c->wa_id, "*Book an Appointment*\n\nTo schedule a visit for your pet, please log in and complete your booking here:\n{$url}\n\nOnce submitted, our Customer Care team will contact you to confirm the details.", $ctx);
                $this->backToMenuHint($c, $ctx);
                break;

            case 'menu_services':
                $this->wa->sendList($c->wa_id, "*Our Services*\n\nPlease select a department to learn more:", 'View Services',
                    array_map(fn ($id) => ['id' => $id, 'title' => $this->services[$id][0]], array_keys($this->services)), null, $ctx);
                $c->step = 'idle';
                $c->save();
                break;

            case 'menu_timings':
                $this->wa->sendText($c->wa_id, "*Timings & Location*\n\nSmall Animal Hospital Mumbai\n[Hospital address — to be confirmed]\n\nWorking Hours\nMonday to Saturday: 9:00 AM – 8:00 PM\nSunday: 9:00 AM – 1:00 PM\n\nPhone: 022 6538 3538\nDirections: [Google Maps link]", $ctx);
                $this->wa->sendButtons($c->wa_id, 'How would you like to proceed?', [
                    ['id' => 'menu_book',      'title' => 'Book a visit'],
                    ['id' => 'menu_emergency', 'title' => 'Emergency help'],
                    ['id' => 'menu_back',      'title' => 'Main menu'],
                ], null, $ctx);
                $c->step = 'idle';
                $c->save();
                break;

            case 'menu_emergency':
                $this->wa->sendText($c->wa_id, "*Emergency Assistance*\n\nIf your pet requires urgent care, please contact us immediately.\n\nCall now: *022 6538 3538*\n\nOr visit us directly at:\n[Hospital address — to be confirmed]\n\nIf possible, please carry any previous medical reports or current medication.", $ctx);
                $this->wa->sendButtons($c->wa_id, 'We can also help with:', [
                    ['id' => 'menu_timings', 'title' => 'Directions'],
                    ['id' => 'menu_talk',    'title' => 'Message the team'],
                    ['id' => 'menu_back',    'title' => 'Main menu'],
                ], null, $ctx);
                $c->step = 'idle';
                $c->save();
                break;

            case 'menu_faq':
                $this->wa->sendList($c->wa_id, "*Common Questions*\n\nPlease select a question:", 'View Questions',
                    array_map(fn ($id) => ['id' => $id, 'title' => $this->faqs[$id][0], 'description' => $this->faqs[$id][1]], array_keys($this->faqs)), null, $ctx);
                $c->step = 'idle';
                $c->save();
                break;

            case 'menu_talk':
                $c->step = 'lead_reason';
                $c->save();
                $this->wa->sendText($c->wa_id, "*Talk to Our Team*\n\nI would be happy to connect you with our Customer Care team. Please type your question or message below, and our team will get back to you shortly.", $ctx);
                break;

            case 'menu_careers':
                $this->wa->sendText($c->wa_id, "*Careers at SAHM*\n\nWe are always looking for passionate people to join our team. Please view our current openings and apply here:\n".route('frontend.join_us'), $ctx);
                $this->backToMenuHint($c, $ctx);
                break;

            default:
                // Unrecognised input (incl. the "Main menu" button) — greet + show menu.
                $this->sendMenu($c, $ctx);
        }
    }

    /** Greeting + the main-menu list (professional tone). */
    private function sendMenu(WhatsAppConversation $c, array $ctx): void
    {
        $c->step = 'idle';
        $c->data = null;
        $c->save();

        $greeting = "Welcome to *Small Animal Hospital Mumbai*.\n\n"
            ."I am your virtual assistant and I am here to help. Please select an option below to continue:";

        $this->wa->sendList($c->wa_id, $greeting, 'Main Menu', [
            ['id' => 'menu_book',      'title' => 'Book an appointment', 'description' => 'Schedule a visit for your pet'],
            ['id' => 'menu_services',  'title' => 'Our services',        'description' => 'Departments and specialities'],
            ['id' => 'menu_timings',   'title' => 'Timings & location',  'description' => 'Hours, address and directions'],
            ['id' => 'menu_emergency', 'title' => 'Emergency help',      'description' => 'Urgent assistance for your pet'],
            ['id' => 'menu_faq',       'title' => 'Common questions',    'description' => 'Fees, reports and visit details'],
            ['id' => 'menu_talk',      'title' => 'Talk to our team',    'description' => 'Connect with our staff'],
            ['id' => 'menu_careers',   'title' => 'Careers',             'description' => 'Current openings'],
        ], null, $ctx);
    }

    /** One service's description + next-step buttons. */
    private function serviceDetail(WhatsAppConversation $c, string $id, array $ctx): void
    {
        [$title, $desc] = $this->services[$id] ?? ['Our Services', ''];
        $this->wa->sendText($c->wa_id, "*{$title}*\n\n{$desc}\n\nWould you like to book an appointment or speak with our team?", $ctx);
        $this->wa->sendButtons($c->wa_id, 'Please choose an option:', [
            ['id' => 'menu_book', 'title' => 'Book appointment'],
            ['id' => 'menu_talk', 'title' => 'Talk to our team'],
            ['id' => 'menu_back', 'title' => 'Main menu'],
        ], null, $ctx);
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

    /** Offer a quick way back to the menu after answering. */
    private function backToMenuHint(WhatsAppConversation $c, array $ctx): void
    {
        $c->step = 'idle';
        $c->save();
        $this->wa->sendButtons($c->wa_id, 'Is there anything else I can help you with?', [
            ['id' => 'menu_back', 'title' => 'Main menu'],
        ], null, $ctx);
    }

    /** "Talk to our team" — capture the message as a Contact Enquiry. */
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

        $this->wa->sendText($c->wa_id, 'Thank you. Your message has been received, and our Customer Care team will contact you shortly.', $ctx);
        $this->backToMenuHint($c, $ctx);
    }
}
