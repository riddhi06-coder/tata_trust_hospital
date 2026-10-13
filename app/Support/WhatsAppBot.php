<?php

namespace App\Support;

use App\Mail\WhatsAppNotificationMail;
use App\Models\AppointmentEnquiry;
use App\Models\ChatbotContent;
use App\Models\AppointmentStatus;
use App\Models\AppointmentUser;
use App\Models\ContactDetails;
use App\Models\MedicalReportRequest;
use App\Models\OurTeam;
use App\Models\Specialities;
use App\Models\WhatsAppBookingRequest;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppTeamQuery;
use App\Services\WhatsAppFortius;
use App\Support\AppointmentNotifier;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Reactive WhatsApp chatbot flow for Small Animal Hospital Mumbai.
 * Runs inside the 24-hour window (user messages first → we reply free-form),
 * so NO approved templates are needed.
 *
 * Warm, elegant tone. Services are pulled LIVE from the database; address,
 * emergency number and map link come from ContactDetails.
 *
 * Booking follows the client-approved flow: a guided New/Existing-client intake
 * collected step-by-step in chat, ending with the login link and a "Customer
 * Care will call to confirm" note. The completed intake is stored
 * (whatsapp_booking_requests) and emailed to Customer Care.
 *
 * "Talk to our team" captures the message, stores it (whatsapp_team_queries)
 * and forwards it to Customer Care by email.
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
        'faq_parking' => ['🅿️ Parking',           'Is parking available?', "Limited parking is available within the building premises on a first-come, first-served basis."],
    ];

    public function handle(string $waId, ?string $profileName, string $text, ?string $interactiveId = null): void
    {
        $convo = WhatsAppConversation::firstOrNew(['wa_id' => $waId]);
        if ($profileName && ! $convo->name) {
            $convo->name = $profileName;
        }
        $convo->last_message_at = now();

        // Record activity in the data column (rolling history, capped at 25 entries).
        $raw  = $interactiveId ?: $text;
        $data = $convo->data ?? [];
        $data['history'] = array_slice(array_merge($data['history'] ?? [], [[
            'at' => now()->toDateTimeString(),
            'in' => mb_substr((string) $raw, 0, 200),
        ]]), -25);
        $convo->data = $data;
        $convo->save();

        // An interactive tap (button/list id) wins; otherwise use the lowercased text.
        $input = $interactiveId ?: strtolower(trim($text));
        $ctx   = ['recipient_name' => $convo->name];

        if (in_array($input, $this->resetWords, true)) {
            $this->sendMenu($convo, $ctx);
            return;
        }

        match ($convo->step) {
            'lead_reason'     => $this->captureReason($convo, $text, $ctx),
            'book_client_type'=> $this->chooseClientType($convo, $text, $interactiveId, $ctx),
            'book_flow'       => $this->continueBooking($convo, $text, $interactiveId, $ctx),
            'report_flow'     => $this->continueReport($convo, $text, $interactiveId, $ctx),
            'book_day_custom' => $this->captureCustomDay($convo, $text, $ctx),
            'book_heard_custom'=> $this->captureCustomHeard($convo, $text, $ctx),
            'book_review'     => $this->reviewRouter($convo, $text, $interactiveId, $ctx),
            'book_edit'       => $this->captureEdit($convo, $text, $interactiveId, $ctx),
            default           => $this->routeMenu($convo, $input, $ctx),
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

        // Admin-created custom menu option → reply with its message.
        if ($row = ChatbotContent::flowRowByField('menu', $input)) {
            $this->wa->sendText($c->wa_id, $row->body ?: 'Our team will get back to you shortly.', $ctx);
            $this->backToMenuHint($c, $ctx);
            return;
        }

        switch ($input) {
            case 'menu_book':          $this->bookAppointment($c, $ctx); break;
            case 'menu_services':      $this->servicesList($c, $ctx); break;
            case 'menu_services_all':  $this->sendLink($c, $ctx, "*Our Services* 🏥\n\nExplore all our departments and specialities on our website:", route('frontend.specialities')); break;
            case 'menu_blog':          $this->sendLink($c, $ctx, "*Blog & Articles* 📝\n\nExplore pet-care tips, heart-warming stories and updates from our team:", route('frontend.blogs')); break;
            case 'menu_timings':       $this->timings($c, $ctx); break;
            case 'menu_emergency':     $this->emergency($c, $ctx); break;
            case 'menu_faq':           $this->faqList($c, $ctx); break;
            case 'menu_reports':       $this->startMedicalReport($c, $ctx); break;
            case 'menu_talk':          $this->startTalk($c, $ctx); break;
            default:                   $this->sendMenu($c, $ctx);   // unrecognised / "Main menu"
        }
    }

    /** Warm greeting (always first) + the main-menu list. Shown every time the menu is requested. */
    private function sendMenu(WhatsAppConversation $c, array $ctx): void
    {
        $c->step = 'idle';
        $this->clearBooking($c);
        $c->save();

        $greeting = ChatbotContent::text('menu.greeting',
            "Hello, and a warm welcome to *Small Animal Hospital Mumbai*! 🐾🐶🐱\n\n"
            ."We're so happy to have you and your companion here. Your pet's health and happiness mean the world to us, and I'm here to help you every step of the way. 🐾\n\n"
            ."How may I assist you and your furry friend today?");

        // Intro ALWAYS first: send the greeting as a plain text message (delivered
        // instantly), then the menu. (An image greeting loads slower and would
        // otherwise arrive AFTER the menu.) The logo is the WhatsApp profile picture.
        $this->wa->sendText($c->wa_id, $greeting, $ctx);

        $menuPrompt = ChatbotContent::text('menu.prompt', 'Please choose an option below:');
        $menuItems  = ChatbotContent::options('menu.items', [
            ['id' => 'menu_book',      'title' => '📅 Book appointment', 'description' => 'Reserve a visit for your pet'],
            ['id' => 'menu_services',  'title' => '🏥 Our services',     'description' => 'Departments & specialities'],
            ['id' => 'menu_timings',   'title' => '📍 Timings & location', 'description' => 'Hours, address & directions'],
            ['id' => 'menu_emergency', 'title' => '🚨 Emergency help',   'description' => 'Urgent care for your pet'],
            ['id' => 'menu_faq',       'title' => '❓ Common questions',  'description' => 'Fees, reports & more'],
            ['id' => 'menu_reports',   'title' => '📄 Medical reports',  'description' => 'Request your pet\'s reports'],
            ['id' => 'menu_blog',      'title' => '📝 Blog & articles',  'description' => 'Pet-care tips & updates'],
            ['id' => 'menu_talk',      'title' => '💬 Talk to our team', 'description' => 'Speak with a real person'],
        ]);

        // Append any admin-created custom menu options (they reply with a message).
        foreach (ChatbotContent::flowRows('menu') as $row) {
            $menuItems[] = ['id' => $row->field_key, 'title' => mb_substr($row->title, 0, 24), 'description' => 'Tap to view'];
        }

        $this->wa->sendList($c->wa_id, $menuPrompt, 'Main Menu', $menuItems, null, $ctx);
    }

    /* -------------------------------------------------------------------- */
    /* Booking flow — guided New/Existing-client intake                      */
    /* -------------------------------------------------------------------- */

    /** Book an appointment → ask whether the pet parent is a new or existing client. */
    private function bookAppointment(WhatsAppConversation $c, array $ctx): void
    {
        $c->step = 'book_client_type';
        $this->clearBooking($c);
        $c->save();

        $this->wa->sendButtons(
            $c->wa_id,
            "*Book an Appointment* 🐾\n\nWonderful — let's get your pet booked! First, are you a new or existing client?",
            [
                ['id' => 'client_new',      'title' => '🆕 New client'],
                ['id' => 'client_existing', 'title' => '👤 Existing client'],
            ],
            null,
            $ctx
        );
    }

    /** Handle the New/Existing choice and start the intake. */
    private function chooseClientType(WhatsAppConversation $c, string $text, ?string $interactiveId, array $ctx): void
    {
        $sel  = $interactiveId ?: strtolower(trim($text));
        $type = str_contains($sel, 'existing') ? 'existing' : 'new';

        // If they pick NEW but this number is already registered with us, they
        // must use "Existing client" (prevents duplicate accounts). Account +
        // OTP login are keyed by mobile, so the WhatsApp number is the signal.
        if ($type === 'new' && $this->findAppointmentUser($c->wa_id)) {
            $this->wa->sendButtons(
                $c->wa_id,
                "Good news — this number is *already registered* with us! 🐾\n\nPlease choose *Existing client* so we can pull up your details instead of creating a duplicate.",
                [['id' => 'client_existing', 'title' => '👤 Existing client']],
                null,
                $ctx
            );
            return; // stay on book_client_type until they pick Existing
        }

        $data = $c->data ?? [];
        $data['booking'] = ['client_type' => $type, 'cursor' => 0, 'answers' => []];
        $c->data = $data;
        $c->step = 'book_flow';
        $c->save();

        $intro = $type === 'existing'
            ? "Welcome back! 🐾 Just a few quick details and we'll set up the visit."
            : "Lovely — welcome to the SAHM family! 🐾 I'll take a few details for your pet's file.";
        $this->wa->sendText($c->wa_id, $intro, $ctx);

        $this->askStep($c, $this->bookingSteps($type, [])[0], $ctx);
    }

    /** Look up an existing client by the WhatsApp number (match last 10 digits). */
    private function findAppointmentUser(string $waId): ?AppointmentUser
    {
        $mobile = substr(preg_replace('/\D+/', '', $waId), -10);
        if (strlen($mobile) < 10) {
            return null;
        }
        return AppointmentUser::whereNull('deleted_by')->where('mobile', $mobile)->first();
    }

    /** Record the current answer and move to the next step (or finalise). */
    private function continueBooking(WhatsAppConversation $c, string $text, ?string $interactiveId, array $ctx): void
    {
        $booking = $c->data['booking'] ?? null;
        if (! $booking) {
            $this->sendMenu($c, $ctx);
            return;
        }

        $steps  = $this->bookingSteps($booking['client_type'], $booking['answers'] ?? []);
        $cursor = (int) ($booking['cursor'] ?? 0);
        $step   = $steps[$cursor] ?? null;
        if (! $step) {
            $this->finaliseBooking($c, $ctx);
            return;
        }

        // For choice steps the webhook passes the tapped label as $text; free text is accepted too.
        $value = trim($text);

        // "Pick a date" on the day step → branch to a free-text date prompt.
        if ($step['key'] === 'preferred_day'
            && ($interactiveId === 'day_pick' || stripos($value, 'pick') !== false)) {
            $c->step = 'book_day_custom';
            $c->save();
            $this->wa->sendText($c->wa_id, "Sure — please *type your preferred date* (e.g. 15 Aug).", $ctx);
            return;
        }

        // "Other" on the how-did-you-hear step → ask them to type it.
        if ($step['key'] === 'how_heard'
            && ($interactiveId === 'heard_other' || strtolower($value) === 'other')) {
            $c->step = 'book_heard_custom';
            $c->save();
            $this->wa->sendText($c->wa_id, "No problem — please *type* how you heard about us.", $ctx);
            return;
        }

        if ($value === '') {
            // Nothing usable — re-ask the same question.
            $this->askStep($c, $step, $ctx);
            return;
        }

        // For DB-driven steps (speciality/doctor), resolve the tapped option id to
        // the full stored value (titles are truncated to WhatsApp's 24-char limit).
        if ($interactiveId && isset($step['values'][$interactiveId])) {
            $value = $step['values'][$interactiveId];
        }

        $this->storeAnswer($c, $step['key'], $value);
        $this->advanceBooking($c, $ctx);
    }

    /** Capture a typed custom date, then continue past the day step. */
    private function captureCustomDay(WhatsAppConversation $c, string $text, array $ctx): void
    {
        $value = trim($text);
        if ($value === '') {
            $this->wa->sendText($c->wa_id, "Please type your preferred date (e.g. 15 Aug).", $ctx);
            return;
        }
        $c->step = 'book_flow';
        $c->save();
        $this->storeAnswer($c, 'preferred_day', $value);
        $this->advanceBooking($c, $ctx);
    }

    /** Capture a typed "Other" how-did-you-hear value, then continue. */
    private function captureCustomHeard(WhatsAppConversation $c, string $text, array $ctx): void
    {
        $value = trim($text);
        if ($value === '') {
            $this->wa->sendText($c->wa_id, "Please type how you heard about us.", $ctx);
            return;
        }
        $c->step = 'book_flow';
        $c->save();
        $this->storeAnswer($c, 'how_heard', $value);
        $this->advanceBooking($c, $ctx);
    }

    /** Persist one answer against the booking cursor and bump the cursor. */
    private function storeAnswer(WhatsAppConversation $c, string $key, string $value): void
    {
        $data = $c->data ?? [];
        $data['booking']['answers'][$key] = mb_substr($value, 0, 500);
        $data['booking']['cursor'] = (int) ($data['booking']['cursor'] ?? 0) + 1;
        $c->data = $data;
        $c->save();
    }

    /** Send the next question, or show the review summary when the intake is complete. */
    private function advanceBooking(WhatsAppConversation $c, array $ctx): void
    {
        $booking = $c->data['booking'];
        $steps   = $this->bookingSteps($booking['client_type'], $booking['answers'] ?? []);
        $cursor  = (int) $booking['cursor'];

        if ($cursor >= count($steps)) {
            $this->sendReview($c, $ctx);
            return;
        }
        $this->askStep($c, $steps[$cursor], $ctx);
    }

    /* -------------------------------------------------------------------- */
    /* Review + edit (so users can fix a typo before confirming)            */
    /* -------------------------------------------------------------------- */

    /** Show a summary of everything entered + a Confirm button. */
    private function sendReview(WhatsAppConversation $c, array $ctx): void
    {
        $c->step = 'book_review';
        $c->save();

        $booking = $c->data['booking'];
        $a       = $booking['answers'] ?? [];
        $labels  = $this->fieldLabels();

        $lines = '';
        foreach ($this->bookingSteps($booking['client_type'], $a) as $step) {
            $k = $step['key'];
            if (isset($a[$k]) && $a[$k] !== '') {
                $lines .= '• *'.($labels[$k] ?? ucfirst($k)).'*: '.$a[$k]."\n";
            }
        }

        $body = "*Please review your details* 📋\n\n".$lines
            ."\nIf everything looks good, tap *Confirm & book*.\nTo change something, just type the field name — e.g. *email*, *pet name* or *day*.";

        $this->wa->sendButtons($c->wa_id, $body, [
            ['id' => 'book_confirm', 'title' => '✅ Confirm & book'],
        ], null, $ctx);
    }

    /** Handle the review step: confirm → finalise; otherwise treat input as a field to edit. */
    private function reviewRouter(WhatsAppConversation $c, string $text, ?string $interactiveId, array $ctx): void
    {
        $sel = $interactiveId ?: strtolower(trim($text));
        if ($sel === 'book_confirm' || in_array($sel, ['confirm', 'book', 'yes', 'done', 'ok'], true)) {
            $this->finaliseBooking($c, $ctx);
            return;
        }

        $key = $this->matchEditField(strtolower(trim($text)), $c->data['booking']['client_type']);
        if (! $key || ! isset($c->data['booking']['answers'][$key])) {
            $this->wa->sendText($c->wa_id, "Sorry, I didn't catch which detail to change. Please type a field name like *email*, *pet name* or *day* — or tap *Confirm & book*.", $ctx);
            return;
        }

        // Begin editing that single field.
        $data = $c->data;
        $data['booking']['editing'] = $key;
        $c->data = $data;
        $c->step = 'book_edit';
        $c->save();

        $this->wa->sendText($c->wa_id, 'Sure — let\'s update that. 🐾', $ctx);
        $this->askStep($c, $this->stepByKey($data['booking']['client_type'], $key, $data['booking']['answers'] ?? []), $ctx);
    }

    /** Capture the edited field's new value, then return to the review. */
    private function captureEdit(WhatsAppConversation $c, string $text, ?string $interactiveId, array $ctx): void
    {
        $key = $c->data['booking']['editing'] ?? null;
        if (! $key) {
            $this->sendReview($c, $ctx);
            return;
        }

        $value = trim($text);

        // "Pick a date" while editing the day → ask for the typed date (stay editing).
        if ($key === 'preferred_day' && ($interactiveId === 'day_pick' || stripos($value, 'pick') !== false)) {
            $this->wa->sendText($c->wa_id, "Please *type your preferred date* (e.g. 15 Aug).", $ctx);
            return;
        }
        // "Other" while editing how-heard → ask them to type it (stay editing).
        if ($key === 'how_heard' && ($interactiveId === 'heard_other' || strtolower($value) === 'other')) {
            $this->wa->sendText($c->wa_id, "Please *type* how you heard about us.", $ctx);
            return;
        }

        if ($value === '') {
            $this->askStep($c, $this->stepByKey($c->data['booking']['client_type'], $key, $c->data['booking']['answers'] ?? []), $ctx);
            return;
        }

        // Resolve DB-driven option ids (speciality/doctor) to their full value.
        $editStep = $this->stepByKey($c->data['booking']['client_type'], $key, $c->data['booking']['answers'] ?? []);
        if ($interactiveId && isset($editStep['values'][$interactiveId])) {
            $value = $editStep['values'][$interactiveId];
        }

        $data = $c->data;
        $data['booking']['answers'][$key] = mb_substr($value, 0, 500);
        // Changing the speciality invalidates a previously chosen doctor.
        if ($key === 'speciality') {
            unset($data['booking']['answers']['doctor']);
        }
        unset($data['booking']['editing']);
        $c->data = $data;
        $c->save();

        $this->sendReview($c, $ctx);
    }

    /** Friendly labels for the review summary / edit matching. */
    private function fieldLabels(): array
    {
        return [
            'parent_name' => 'Name', 'address' => 'Address', 'email' => 'Email', 'mobile' => 'Mobile',
            'patient_uid' => 'Patient UID',
            'pincode' => 'PIN code', 'pet_name' => 'Pet name', 'species' => 'Species', 'sex' => 'Sex',
            'breed' => 'Breed', 'colour' => 'Colour', 'dob_age' => 'Age / DOB', 'weight' => 'Weight',
            'neutered' => 'Neutered', 'complaint' => 'Concern', 'how_heard' => 'How heard', 'referred_by' => 'Referred by',
            'reason' => 'Visit reason', 'speciality' => 'Speciality', 'doctor' => 'Doctor',
            'preferred_day' => 'Day', 'preferred_time' => 'Time',
        ] + ChatbotContent::customLabels('booking');
    }

    /** Find a booking step by its key (for re-asking on edit). */
    private function stepByKey(string $clientType, string $key, array $answers = []): array
    {
        foreach ($this->bookingSteps($clientType, $answers) as $step) {
            if ($step['key'] === $key) {
                return $step;
            }
        }
        return ['key' => $key, 'type' => 'text', 'q' => 'Please enter the new value:'];
    }

    /** Map a typed field name to its key (most-specific phrases first). */
    private function matchEditField(string $text, string $clientType): ?string
    {
        $map = [
            'pet_name'       => ['pet name', 'petname', 'pet\'s name'],
            'species'        => ['species', 'pet type', 'dog', 'cat'],
            'sex'            => ['sex', 'gender'],
            'breed'          => ['breed'],
            'colour'         => ['colour', 'color'],
            'dob_age'        => ['age', 'dob', 'birth'],
            'weight'         => ['weight'],
            'neutered'       => ['neuter', 'spay'],
            'complaint'      => ['concern', 'complaint', 'symptom', 'reason'],
            'how_heard'      => ['how heard', 'heard', 'hear'],
            'referred_by'    => ['refer'],
            'doctor'         => ['doctor', 'doc', 'vet'],
            'speciality'     => ['speciality', 'specialty', 'department'],
            'preferred_day'  => ['day', 'date'],
            'preferred_time' => ['time', 'slot'],
            'email'          => ['email', 'mail'],
            'address'        => ['address'],
            'pincode'        => ['pincode', 'pin code', 'pin', 'zip'],
            'patient_uid'    => ['uid', 'patient id', 'patient uid', 'patient-id'],
            'mobile'         => ['mobile', 'phone', 'contact number'],
            'parent_name'    => ['name', 'owner'],
        ];

        foreach ($map as $key => $keywords) {
            foreach ($keywords as $kw) {
                if (str_contains($text, $kw)) {
                    return $key;
                }
            }
        }
        return null;
    }

    /** Render a single intake step (text prompt, buttons, or a list). */
    private function askStep(WhatsAppConversation $c, array $step, array $ctx): void
    {
        if (($step['type'] ?? 'text') === 'choice') {
            $rows = array_map(fn ($ch) => ['id' => $ch['id'], 'title' => $ch['title']], $step['choices']);
            if (! empty($step['list']) || count($rows) > 3) {
                $this->wa->sendList($c->wa_id, $step['q'], 'Choose', $rows, null, $ctx);
            } else {
                $this->wa->sendButtons($c->wa_id, $step['q'], $rows, null, $ctx);
            }
            return;
        }

        $this->wa->sendText($c->wa_id, $step['q'], $ctx);
    }

    /**
     * Ordered intake steps. New clients give a full pet-file intake; existing
     * clients give just enough to schedule. Keys map to DB columns.
     */
    private function bookingSteps(string $clientType, array $answers = []): array
    {
        $reason = ['key' => 'reason', 'type' => 'choice', 'list' => true, 'q' => "Got it. What's the visit for?", 'choices' => [
            ['id' => 'reason_vac', 'title' => '💉 Vaccination'],
            ['id' => 'reason_gen', 'title' => '🩺 General consultation'],
            ['id' => 'reason_sur', 'title' => '🔬 Surgery / procedure'],
            ['id' => 'reason_den', 'title' => '🦷 Dental / other'],
        ]];

        // Speciality + doctor are DB-driven (admins manage the doctor list and the
        // "selectable in WhatsApp" toggle). The doctor list depends on the chosen
        // speciality, so it's resolved from the answers collected so far.
        $speciality = $this->specialityStep();
        $doctor     = $this->doctorStep($answers['speciality'] ?? null);

        // Admin-created custom questions, injected just before scheduling (day/time).
        $custom = ChatbotContent::customSteps('booking');
        $day = ['key' => 'preferred_day', 'type' => 'choice', 'q' => "Which day works best for you?", 'choices' => [
            ['id' => 'day_today', 'title' => 'Today'],
            ['id' => 'day_tomorrow', 'title' => 'Tomorrow'],
            ['id' => 'day_pick', 'title' => '📆 Pick a date'],
        ]];
        $time = ['key' => 'preferred_time', 'type' => 'choice', 'q' => "And a preferred time?", 'choices' => [
            ['id' => 'time_morning', 'title' => '🌅 Morning (9–12)'],
            ['id' => 'time_afternoon', 'title' => '☀️ Afternoon (12–4)'],
            ['id' => 'time_evening', 'title' => '🌆 Evening (4–8)'],
        ]];

        // "How did you hear about us?" — a fixed option set (new clients only).
        $heard = ['key' => 'how_heard', 'type' => 'choice', 'list' => true, 'q' => "How did you *hear about us*?", 'choices' => [
            ['id' => 'heard_google',   'title' => 'Google Search'],
            ['id' => 'heard_maps',     'title' => 'Google Maps'],
            ['id' => 'heard_social',   'title' => 'Social Media (Insta/FB)'],
            ['id' => 'heard_friend',   'title' => 'Friend/Family Referral'],
            ['id' => 'heard_vet',      'title' => 'Veterinarian Referral'],
            ['id' => 'heard_previous', 'title' => 'Previous Visit'],
            ['id' => 'heard_tata',     'title' => 'Tata Trusts/Tata Group'],
            ['id' => 'heard_other',    'title' => 'Other'],
        ]];

        if ($clientType === 'existing') {
            // Existing clients self-identify (Patient UID + mobile) — no registration fields.
            return $this->overlaySteps(array_merge([
                ['key' => 'parent_name', 'type' => 'text', 'q' => "What's the *pet parent's name*?"],
                ['key' => 'pet_name',    'type' => 'text', 'q' => "And your *pet's name*?"],
                ['key' => 'patient_uid', 'type' => 'text', 'q' => "Your *Patient ID / UID* with us? (type 'NA' if you don't have it)"],
                ['key' => 'mobile',      'type' => 'text', 'q' => "Best *mobile number* to reach you?"],
                $reason, $speciality, $doctor,
            ], $custom, [$day, $time]), 'book');
        }

        return $this->overlaySteps(array_merge([
            // Pet parent
            ['key' => 'parent_name', 'type' => 'text', 'q' => "What's the *pet parent's full name*?"],
            ['key' => 'address',     'type' => 'text', 'q' => "Your *address*?"],
            ['key' => 'email',       'type' => 'text', 'q' => "Your *email address*?"],
            ['key' => 'mobile',      'type' => 'text', 'q' => "Best *mobile number* to reach you?"],
            ['key' => 'pincode',     'type' => 'text', 'q' => "Your *PIN code*?"],
            // Pet
            ['key' => 'pet_name',    'type' => 'text', 'q' => "Now your companion 🐾 — what's your *pet's name*?"],
            ['key' => 'species', 'type' => 'choice', 'q' => "Which pet are we seeing?", 'choices' => [
                ['id' => 'sp_dog', 'title' => '🐶 Dog'],
                ['id' => 'sp_cat', 'title' => '🐱 Cat'],
            ]],
            ['key' => 'sex', 'type' => 'choice', 'q' => "Your pet's *sex*?", 'choices' => [
                ['id' => 'sex_m', 'title' => '♂️ Male'],
                ['id' => 'sex_f', 'title' => '♀️ Female'],
            ]],
            ['key' => 'breed',   'type' => 'text', 'q' => "*Breed*? (type 'NA' if unsure)"],
            ['key' => 'colour',  'type' => 'text', 'q' => "*Colour*?"],
            ['key' => 'dob_age', 'type' => 'text', 'q' => "*Date of birth or age*?"],
            ['key' => 'weight',  'type' => 'text', 'q' => "*Weight*? (type 'NA' if unsure)"],
            ['key' => 'neutered', 'type' => 'choice', 'q' => "Is your pet *neutered / spayed*?", 'choices' => [
                ['id' => 'neu_y', 'title' => 'Yes'],
                ['id' => 'neu_n', 'title' => 'No'],
            ]],
            ['key' => 'complaint',   'type' => 'text', 'q' => "What's the *main concern / reason* for the visit?"],
            $heard,
            ['key' => 'referred_by', 'type' => 'text', 'q' => "*Referred by*? (type 'NA' if none)"],
            // Visit scheduling
            $reason, $speciality, $doctor,
        ], $custom, [$day, $time]), 'book');
    }

    /**
     * Overlay admin-editable content (ChatbotContent) onto a set of steps: the
     * question prompt and, for choice steps, the answer options + their order.
     * Keys absent from the DB keep their built-in defaults, so the flow never
     * breaks. Dynamic steps (speciality/doctor) have no stored options, so their
     * DB-driven choices pass through untouched — only their prompt is editable.
     */
    private function overlaySteps(array $steps, string $prefix): array
    {
        foreach ($steps as &$step) {
            $key = $prefix.'.'.$step['key'];
            $step['q'] = ChatbotContent::text($key, $step['q']);
            if (($step['type'] ?? 'text') === 'choice' && isset($step['choices'])) {
                $step['choices'] = ChatbotContent::options($key, $step['choices']);
            }
        }
        unset($step);

        return $steps;
    }

    /**
     * Speciality choice step — only specialities that have at least one doctor
     * marked "selectable in WhatsApp", plus a "Not sure / Any" escape hatch.
     * The 'values' map resolves a tapped option id back to the full name to store.
     */
    private function specialityStep(): array
    {
        $choices = [];
        $values  = [];
        foreach ($this->bookableSpecialities() as $spec) {
            $id = 'spec_'.$spec->id;
            $choices[] = ['id' => $id, 'title' => mb_substr($spec->speciality, 0, 24)];
            $values[$id] = $spec->speciality;
        }
        $choices[] = ['id' => 'spec_any', 'title' => 'Not sure / Any'];
        $values['spec_any'] = 'No preference';

        return [
            'key' => 'speciality', 'type' => 'choice', 'list' => true, 'values' => $values,
            'q' => "Which *speciality* do you need?",
            'choices' => $choices,
        ];
    }

    /**
     * Doctor choice step — doctors in the chosen speciality that are selectable on
     * WhatsApp, plus "No preference". If no speciality (or no doctors) is resolved,
     * only "No preference" is offered.
     */
    private function doctorStep(?string $specialityName): array
    {
        $choices = [];
        $values  = [];
        foreach ($this->bookableDoctorsFor($specialityName) as $doc) {
            $id = 'doc_'.$doc->id;
            $choices[] = ['id' => $id, 'title' => mb_substr($doc->name, 0, 24)];
            $values[$id] = $doc->name;
        }
        $choices[] = ['id' => 'doc_any', 'title' => 'No preference'];
        $values['doc_any'] = 'No preference';

        return [
            'key' => 'doctor', 'type' => 'choice', 'list' => true, 'values' => $values,
            'q' => "Any *preferred doctor*?",
            'choices' => $choices,
        ];
    }

    /** Specialities that have at least one WhatsApp-selectable doctor. */
    private function bookableSpecialities()
    {
        return Specialities::whereNull('deleted_by')
            ->whereHas('details.doctors', fn ($q) => $q->where('bookable_on_whatsapp', true)->whereNull('our_teams.deleted_by'))
            ->orderBy('speciality')
            ->get();
    }

    /** WhatsApp-selectable doctors for a speciality (matched by name). */
    private function bookableDoctorsFor(?string $specialityName)
    {
        $specialityName = trim((string) $specialityName);
        if ($specialityName === '' || strcasecmp($specialityName, 'No preference') === 0) {
            return collect();
        }

        $spec = Specialities::whereNull('deleted_by')->where('speciality', $specialityName)->first();
        if (! $spec) {
            return collect();
        }

        return OurTeam::whereNull('deleted_by')
            ->where('bookable_on_whatsapp', true)
            ->whereHas('specialityDetails', fn ($q) => $q->where('speciality_id', $spec->id))
            ->orderBy('name')
            ->get()
            ->unique('id')
            ->take(9)
            ->values();
    }

    /**
     * Save the completed intake, create the appointment directly (source=whatsapp)
     * running the same post-booking process as the website, and confirm with a reference.
     */
    private function finaliseBooking(WhatsAppConversation $c, array $ctx): void
    {
        $booking = $c->data['booking'] ?? ['client_type' => 'new', 'answers' => []];
        $a = $booking['answers'] ?? [];
        $clientType = $booking['client_type'] ?? 'new';

        // 1) Persist the raw chat intake (audit record of exactly what was collected).
        try {
            $request = WhatsAppBookingRequest::create(array_merge(
                ['wa_id' => $c->wa_id, 'client_type' => $clientType, 'status' => 'new'],
                array_intersect_key($a, array_flip([
                    'parent_name', 'address', 'email', 'mobile', 'pincode', 'patient_uid',
                    'pet_name', 'species', 'sex', 'breed', 'colour', 'dob_age', 'weight',
                    'neutered', 'complaint', 'how_heard', 'referred_by',
                    'reason', 'preferred_day', 'preferred_time',
                ]))
            ));
        } catch (\Throwable $e) {
            Log::error('WhatsApp booking save failed: '.$e->getMessage(), ['wa_id' => $c->wa_id]);
            $request = null;
        }

        // 2) Create the appointment directly + run website-equivalent notifications.
        $enquiry = null;
        try {
            $enquiry = $this->createAppointmentFromBooking($a, $clientType, $c->wa_id, $request);
        } catch (\Throwable $e) {
            Log::error('WhatsApp appointment create failed: '.$e->getMessage(), ['wa_id' => $c->wa_id]);
        }

        // Reset conversation state.
        $c->step = 'idle';
        $this->clearBooking($c);
        $c->save();

        // 3) Confirmation message + reference number (details were already reviewed).
        //    No website booking link — the appointment is already on record.
        $ref     = $enquiry ? $enquiry->reference() : ($request ? $request->reference() : null);
        $species = $this->normalizePetType($a['species'] ?? null);
        $petLine = trim(($a['pet_name'] ?? 'your pet').' '.($species ? '('.ucfirst($species).')' : ''));
        $summary = "*Appointment request received* 🎉\n\n"
            ."🐾 {$petLine}\n"
            .(isset($a['reason']) ? "🩺 {$a['reason']}\n" : '')
            .((isset($a['doctor']) && strcasecmp($a['doctor'], 'No preference') !== 0) ? "👨‍⚕️ {$a['doctor']}\n" : '')
            .(isset($a['preferred_day']) ? "📆 {$a['preferred_day']}".(isset($a['preferred_time']) ? " · {$a['preferred_time']}" : '')."\n" : '')
            .(isset($a['parent_name']) ? "👤 {$a['parent_name']}\n" : '')
            ."📱 ".$c->wa_id."\n\n"
            ."Thank you".(isset($a['parent_name']) ? ', '.strtok($a['parent_name'], ' ') : '')."! This is a *tentative* booking — our Customer Care team will call you shortly to confirm the date and time."
            .($ref ? "\n🔖 Ref: #{$ref}" : '');
        $this->wa->sendText($c->wa_id, $summary, $ctx);

        // Re-show the main menu a little later (not immediately) so the confirmation
        // lands first. Needs a queue worker for the real delay; with sync it sends now.
        try {
            \App\Jobs\SendWhatsAppMainMenu::dispatch($c->wa_id)->delay(now()->addSeconds(30));
        } catch (\Throwable $e) {
            Log::error('WhatsApp delayed menu dispatch failed: '.$e->getMessage());
        }
    }

    /**
     * Turn a completed chat intake into an AppointmentEnquiry (source=whatsapp),
     * mirroring HomeController::appointment_store so WhatsApp bookings land in the
     * same admin dashboard and run the same notifications.
     */
    private function createAppointmentFromBooking(array $a, string $clientType, string $waId, ?WhatsAppBookingRequest $request): AppointmentEnquiry
    {
        $mobile = preg_replace('/\D+/', '', $a['mobile'] ?? '');
        $mobile = $mobile !== '' ? substr($mobile, -10) : substr(preg_replace('/\D+/', '', $waId), -10);

        // Find-or-create the client profile keyed by mobile (as the website does on login).
        $user = AppointmentUser::whereNull('deleted_by')->where('mobile', $mobile)->first();
        if (! $user) {
            $user = AppointmentUser::create([
                'mobile'           => $mobile,
                'name'             => $a['parent_name'] ?? null,
                'email'            => $a['email'] ?? null,
                'address'          => $a['address'] ?? null,
                'pincode'          => $a['pincode'] ?? null,
                'last_verified_at' => now(),
            ]);
        } elseif (! empty($a['parent_name'])) {
            $user->update(array_filter([
                'name'    => $a['parent_name'] ?? null,
                'email'   => $a['email'] ?? null,
                'address' => $a['address'] ?? null,
                'pincode' => $a['pincode'] ?? null,
            ]));
        }

        // Fold the chat-only extras (not columns on appointment_enquiries) into the reason.
        $reason = $a['reason'] ?? $a['complaint'] ?? 'WhatsApp appointment request';
        $extras = array_filter([
            'Patient UID'    => $a['patient_uid'] ?? null,
            'Breed'          => $a['breed'] ?? null,
            'Colour'         => $a['colour'] ?? null,
            'Weight'         => $a['weight'] ?? null,
            'Neutered'       => $a['neutered'] ?? null,
            'How heard'      => $a['how_heard'] ?? null,
            'Referred by'    => $a['referred_by'] ?? null,
            'Preferred time' => $a['preferred_time'] ?? null,
            'Preferred day'  => $a['preferred_day'] ?? null,
        ], fn ($v) => $v !== null && $v !== '' && strtoupper((string) $v) !== 'NA');

        // Fold in any admin-created custom question answers (keyed by their label).
        foreach (ChatbotContent::customLabels('booking') as $fieldKey => $label) {
            $val = $a[$fieldKey] ?? null;
            if ($val !== null && $val !== '' && strtoupper((string) $val) !== 'NA') {
                $extras[$label] = $val;
            }
        }

        if ($extras) {
            $reason .= "\n\n-- WhatsApp intake --\n".collect($extras)->map(fn ($v, $k) => "{$k}: {$v}")->implode("\n");
        }

        // Preferred speciality / doctor — drop the "No preference" sentinel.
        $speciality = $a['speciality'] ?? null;
        $doctor     = $a['doctor'] ?? null;
        $speciality = ($speciality && strcasecmp($speciality, 'No preference') !== 0) ? $speciality : null;
        $doctor     = ($doctor && strcasecmp($doctor, 'No preference') !== 0) ? $doctor : null;

        $defaultStatusId = AppointmentStatus::whereNull('deleted_by')
            ->where('is_default', true)
            ->value('id');

        // Per-source running reference number (whatsapp WA-0001…).
        $refNo = (int) AppointmentEnquiry::where('source', 'whatsapp')->max('ref_no') + 1;

        $enquiry = AppointmentEnquiry::create([
            'appointment_user_id'   => $user->id,
            'owner_name'            => $a['parent_name'] ?? ($user->name ?: 'WhatsApp client'),
            'mobile'                => $mobile,
            'email'                 => $a['email'] ?? null,
            'address'               => $a['address'] ?? null,
            'pincode'               => $a['pincode'] ?? null,
            'pet_name'              => $a['pet_name'] ?? null,
            'pet_age'               => $a['dob_age'] ?? null,
            'pet_type'              => $this->normalizePetType($a['species'] ?? null),
            'pet_gender'            => $this->normalizePetGender($a['sex'] ?? null),
            'consult_type'          => $clientType === 'existing' ? 'followup' : 'first',
            'reason'                => $reason,
            'speciality'            => $speciality,
            'preferred_doctor'      => $doctor,
            'appointment_date'      => $this->normalizeBookingDay($a['preferred_day'] ?? null),
            'appointment_status_id' => $defaultStatusId,
            'source'                => 'whatsapp',
            'ref_no'                => $refNo,
        ]);

        // Link + close the raw intake record so its data can't be re-converted.
        if ($request) {
            $request->update(['converted_at' => now(), 'status' => 'converted']);
        }

        AppointmentNotifier::notifyNewBooking($enquiry);

        return $enquiry;
    }

    /** "🐶 Dog" / "cat" → the pet_type enum value, or null if not a dog/cat. */
    private function normalizePetType(?string $v): ?string
    {
        if (! $v) {
            return null;
        }
        $v = strtolower($v);
        if (str_contains($v, 'cat')) {
            return 'cat';
        }
        if (str_contains($v, 'dog')) {
            return 'dog';
        }
        return null;
    }

    /** "♀️ Female" / "♂️ Male" → the pet_gender enum value, or null. */
    private function normalizePetGender(?string $v): ?string
    {
        if (! $v) {
            return null;
        }
        $v = strtolower($v);
        // Check female first — "female" contains the substring "male".
        if (str_contains($v, 'female') || str_contains($v, '♀')) {
            return 'female';
        }
        if (str_contains($v, 'male') || str_contains($v, '♂')) {
            return 'male';
        }
        return null;
    }

    /**
     * Best-effort parse of the chosen day into a date. "Today"/"Tomorrow" and typed
     * dates (e.g. "15 Aug") are resolved; anything unparseable is left null for the
     * Customer Care team to confirm on the call.
     */
    private function normalizeBookingDay(?string $v): ?string
    {
        $v = trim((string) $v);
        if ($v === '') {
            return null;
        }
        $lower = strtolower($v);
        if (str_contains($lower, 'today')) {
            return now()->toDateString();
        }
        if (str_contains($lower, 'tomorrow')) {
            return now()->addDay()->toDateString();
        }
        try {
            $date = Carbon::parse($v);
            // Push past dates (e.g. a weekday name already passed this week) forward a bit.
            return $date->isPast() && $date->isToday() === false ? $date->addYear()->toDateString() : $date->toDateString();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /** Public entry for the delayed-menu job: re-show the welcome + main menu. */
    public function sendMainMenu(string $waId): void
    {
        $convo = WhatsAppConversation::firstOrNew(['wa_id' => $waId]);
        $this->sendMenu($convo, ['recipient_name' => $convo->name]);
    }

    /** Email the completed booking intake to Customer Care. */
    private function emailBooking(WhatsAppBookingRequest $r): void
    {
        $adminTo = config('mail.admin_notifications.appointment', config('mail.admin_notification'));
        if (! $adminTo) {
            return;
        }

        $rows = [
            'Reference'    => '#'.$r->reference(),
            'Client type'  => ucfirst((string) $r->client_type),
            'Pet parent'   => $r->parent_name,
            'Mobile'       => $r->mobile ?: $r->wa_id,
            'Email'        => $r->email,
            'Address'      => $r->address,
            'PIN code'     => $r->pincode,
            'Pet name'     => $r->pet_name,
            'Species'      => $r->species,
            'Sex'          => $r->sex,
            'Breed'        => $r->breed,
            'Colour'       => $r->colour,
            'DOB / Age'    => $r->dob_age,
            'Weight'       => $r->weight,
            'Neutered'     => $r->neutered,
            'Complaint'    => $r->complaint,
            'How heard'    => $r->how_heard,
            'Referred by'  => $r->referred_by,
            'Visit reason' => $r->reason,
            'Preferred day'=> $r->preferred_day,
            'Preferred time'=> $r->preferred_time,
            'WhatsApp'     => $r->wa_id,
        ];

        try {
            Mail::to($adminTo)->send(new WhatsAppNotificationMail(
                'New WhatsApp Booking Request',
                'A pet parent has requested an appointment via the WhatsApp assistant. Please call to confirm.',
                $rows,
                'Tentative request — confirm the exact slot by phone.',
                file_exists(public_path('frontend/assets/img/logo/tata-trust-logo.webp'))
            ));
            CommunicationLogger::log([
                'channel' => 'email', 'type' => 'wa_booking_admin', 'recipient' => $adminTo,
                'recipient_name' => $r->parent_name, 'subject' => 'New WhatsApp Booking Request',
                'message' => 'WhatsApp booking #'.$r->reference().' from '.$r->parent_name, 'status' => 'sent',
            ]);
        } catch (\Throwable $e) {
            Log::error('WhatsApp booking mail failed: '.$e->getMessage(), ['booking_id' => $r->id]);
            CommunicationLogger::log([
                'channel' => 'email', 'type' => 'wa_booking_admin', 'recipient' => $adminTo,
                'subject' => 'New WhatsApp Booking Request', 'status' => 'failed', 'error' => $e->getMessage(),
            ]);
        }
    }

    private function clearBooking(WhatsAppConversation $c): void
    {
        $data = $c->data ?? [];
        unset($data['booking']);
        $c->data = $data;
    }

    /* -------------------------------------------------------------------- */
    /* Services                                                              */
    /* -------------------------------------------------------------------- */

    /** "Our services" — live list of specialities from the database. */
    private function servicesList(WhatsAppConversation $c, array $ctx): void
    {
        $items = Specialities::whereNull('deleted_by')->orderBy('id')->limit(9)->get();

        if ($items->isEmpty()) {
            $this->sendLink($c, $ctx, "*Our Services* 🏥\n\nExplore our departments and specialities on our website:", route('frontend.specialities'));
            return;
        }

        $rows = $items->map(fn ($s) => ['id' => 'svc_'.$s->id, 'title' => $s->speciality])->values()->all();
        $rows[] = ['id' => 'menu_services_all', 'title' => '🔎 View all on website'];

        $this->wa->sendList($c->wa_id, "*Our Services* 🏥\n\nHere's what we care for at SAHM. Tap any to learn more:", 'View Services', $rows, null, $ctx);
        $c->step = 'idle';
        $c->save();
    }

    /** One speciality → short intro + previewed link to its website page. */
    private function serviceDetail(WhatsAppConversation $c, string $input, array $ctx): void
    {
        $id = (int) str_replace('svc_', '', $input);
        $s  = Specialities::whereNull('deleted_by')->find($id);

        if (! $s) {
            $this->sendMenu($c, $ctx);
            return;
        }

        $url = route('frontend.specialities_details', $s->slug);
        $this->sendLink($c, $ctx, "*{$s->speciality}* 🐾\n\nLearn all about our {$s->speciality} care here:", $url);
    }

    /* -------------------------------------------------------------------- */
    /* Timings / emergency / FAQ                                             */
    /* -------------------------------------------------------------------- */

    /** Timings & location — pulled from ContactDetails, with a previewed Directions link. */
    private function timings(WhatsAppConversation $c, array $ctx): void
    {
        $hours = ChatbotContent::text('info.hours', "🕐 *Working Hours*\nAll days (Mon–Sun): 9:30 AM – 6:30 PM\n🚑 Emergency: 24×7");
        $body = "*Timings & Location* 📍\n\n"
            .$this->address()."\n\n"
            .$hours."\n\n"
            ."📞 ".$this->emergencyNo();

        if ($map = $this->mapUrl()) {
            $body .= "\n\n🗺️ Get directions:\n".$map;
        }

        $this->wa->sendText($c->wa_id, $body, $ctx);
        $this->backToMenuHint($c, $ctx);
    }

    /** Emergency — phone-first, with a previewed Directions link. */
    private function emergency(WhatsAppConversation $c, array $ctx): void
    {
        $no    = $this->emergencyNo();
        $intro = ChatbotContent::text('info.emergency', "*Emergency Help* 🚨\n\nYour pet's wellbeing can't wait — we're here for you. Please contact us right away.");
        $body  = $intro."\n\n"
            ."📞 Call now: *{$no}*\n\n"
            ."🏥 Or come straight to the hospital:\n".$this->address();

        if ($map = $this->mapUrl()) {
            $body .= "\n\n🗺️ Get directions:\n".$map;
        }

        $body .= "\n\nIf you can, please bring any past reports or the medicine your pet is on.";

        $this->wa->sendText($c->wa_id, $body, $ctx);
        $this->backToMenuHint($c, $ctx);
    }

    /** FAQ list. */
    private function faqList(WhatsAppConversation $c, array $ctx): void
    {
        $rows = array_map(fn ($id) => ['id' => $id, 'title' => $this->faqs[$id][0], 'description' => $this->faqs[$id][1]], array_keys($this->faqs));

        // Append any admin-created custom FAQs.
        foreach (ChatbotContent::flowRows('faq') as $row) {
            $rows[] = ['id' => $row->field_key, 'title' => mb_substr($row->title, 0, 24), 'description' => 'Tap to view'];
        }

        $this->wa->sendList($c->wa_id, "*Common Questions* ❓\n\nPlease select a question:", 'View Questions', $rows, null, $ctx);
        $c->step = 'idle';
        $c->save();
    }

    /** One FAQ answer + follow-up buttons. */
    private function faqAnswer(WhatsAppConversation $c, string $id, array $ctx): void
    {
        // Admin-created custom FAQ → its own answer; otherwise a built-in (editable) answer.
        if ($custom = ChatbotContent::flowRowByField('faq', $id)) {
            $answer = $custom->body ?: 'Our team will be glad to help with this.';
        } else {
            $answer = ChatbotContent::text('faq.'.$id, $this->faqs[$id][2] ?? 'I am sorry, I could not find that answer.');
        }
        $this->wa->sendText($c->wa_id, $answer."\n\nWas this helpful?", $ctx);
        $this->wa->sendButtons($c->wa_id, 'Please let me know:', [
            ['id' => 'menu_back', 'title' => '👍 Yes, thank you'],
            ['id' => 'menu_talk', 'title' => '💬 Talk to our team'],
        ], null, $ctx);
        $c->step = 'idle';
        $c->save();
    }

    /* -------------------------------------------------------------------- */
    /* Talk to our team                                                      */
    /* -------------------------------------------------------------------- */

    /** "Talk to our team" — begin capturing the user's message. */
    private function startTalk(WhatsAppConversation $c, array $ctx): void
    {
        $c->step = 'lead_reason';
        $c->save();
        $this->wa->sendText($c->wa_id, "*Talk to Our Team* 💬\n\nOf course — I'll connect you with our Customer Care team. Please share your question below, and we'll get back to you shortly.", $ctx);
    }

    /** Save the captured message, forward it to Customer Care by email, then confirm. */
    private function captureReason(WhatsAppConversation $c, string $text, array $ctx): void
    {
        $message = trim($text);

        try {
            $query = WhatsAppTeamQuery::create([
                'wa_id'   => $c->wa_id,
                'name'    => $c->name,
                'message' => $message,
                'status'  => 'new',
            ]);
            $this->emailTeamQuery($query);
        } catch (\Throwable $e) {
            Log::error('WhatsApp team query save failed: '.$e->getMessage(), ['wa_id' => $c->wa_id]);
        }

        // Also keep the enquiry on the conversation record (data column).
        $data = $c->data ?? [];
        $data['enquiries'][] = ['at' => now()->toDateTimeString(), 'message' => $message];
        $c->data = $data;
        $c->step = 'idle';
        $c->save();

        $this->wa->sendText($c->wa_id, "Thank you! Your message has reached our Customer Care team, and they'll get back to you shortly.", $ctx);
        $this->backToMenuHint($c, $ctx);
    }

    /** Forward a "talk to our team" message to Customer Care by email. */
    private function emailTeamQuery(WhatsAppTeamQuery $q): void
    {
        $adminTo = config('mail.admin_notifications.contact', config('mail.admin_notification'));
        if (! $adminTo) {
            return;
        }

        $rows = [
            'From'     => $q->name ?: 'WhatsApp User',
            'WhatsApp' => $q->wa_id,
            'Message'  => $q->message,
        ];

        try {
            Mail::to($adminTo)->send(new WhatsAppNotificationMail(
                'New WhatsApp Team Query',
                'A pet parent has asked to speak with the team via the WhatsApp assistant.',
                $rows,
                'Please follow up with the pet parent on WhatsApp or by phone.',
                file_exists(public_path('frontend/assets/img/logo/tata-trust-logo.webp'))
            ));
            CommunicationLogger::log([
                'channel' => 'email', 'type' => 'wa_team_query_admin', 'recipient' => $adminTo,
                'recipient_name' => $q->name, 'subject' => 'New WhatsApp Team Query',
                'message' => 'WhatsApp team query from '.($q->name ?: $q->wa_id), 'status' => 'sent',
            ]);
        } catch (\Throwable $e) {
            Log::error('WhatsApp team query mail failed: '.$e->getMessage(), ['query_id' => $q->id]);
            CommunicationLogger::log([
                'channel' => 'email', 'type' => 'wa_team_query_admin', 'recipient' => $adminTo,
                'subject' => 'New WhatsApp Team Query', 'status' => 'failed', 'error' => $e->getMessage(),
            ]);
        }
    }

    /* -------------------------------------------------------------------- */
    /* Medical report requests                                              */
    /* -------------------------------------------------------------------- */

    /** The ordered questions for a medical-report request (all free text). */
    private function reportSteps(): array
    {
        return $this->overlaySteps(array_merge([
            ['key' => 'client_name',  'type' => 'text', 'q' => "*Medical Reports* 📄\n\nHappy to help you get your pet's reports. First, the *pet parent's name*?"],
            ['key' => 'patient_name', 'type' => 'text', 'q' => "Your *pet's name*?"],
            ['key' => 'patient_uid',  'type' => 'text', 'q' => "Your *Patient ID / UID* with us? (type 'NA' if you don't have it)"],
            ['key' => 'mobile',       'type' => 'text', 'q' => "Best *mobile number* to reach you?"],
            ['key' => 'report_name',  'type' => 'text', 'q' => "Which *report* do you need? (e.g. blood test, X-ray, discharge summary)"],
        ], ChatbotContent::customSteps('report')), 'report');
    }

    /** Begin the medical-report request flow. */
    private function startMedicalReport(WhatsAppConversation $c, array $ctx): void
    {
        $data = $c->data ?? [];
        $data['report'] = ['cursor' => 0, 'answers' => []];
        $c->data = $data;
        $c->step = 'report_flow';
        $c->save();

        $this->askStep($c, $this->reportSteps()[0], $ctx);
    }

    /** Record the current answer and ask the next question, or finalise. */
    private function continueReport(WhatsAppConversation $c, string $text, ?string $interactiveId, array $ctx): void
    {
        $report = $c->data['report'] ?? null;
        if (! $report) {
            $this->sendMenu($c, $ctx);
            return;
        }

        $steps  = $this->reportSteps();
        $cursor = (int) ($report['cursor'] ?? 0);
        $step   = $steps[$cursor] ?? null;
        if (! $step) {
            $this->finaliseReport($c, $ctx);
            return;
        }

        $value = trim($text);
        if ($value === '') {
            $this->askStep($c, $step, $ctx);
            return;
        }

        // Resolve a tapped multiple-choice option id to its full stored value.
        if ($interactiveId && isset($step['values'][$interactiveId])) {
            $value = $step['values'][$interactiveId];
        }

        $data = $c->data;
        $data['report']['answers'][$step['key']] = mb_substr($value, 0, 255);
        $data['report']['cursor'] = $cursor + 1;
        $c->data = $data;
        $c->save();

        $next = $steps[$cursor + 1] ?? null;
        if ($next) {
            $this->askStep($c, $next, $ctx);
        } else {
            $this->finaliseReport($c, $ctx);
        }
    }

    /** Save the request, raise a notification + email the team, then confirm. */
    private function finaliseReport(WhatsAppConversation $c, array $ctx): void
    {
        $a = $c->data['report']['answers'] ?? [];

        // Answers to admin-created custom questions (keyed by their label).
        $extra = [];
        foreach (ChatbotContent::customLabels('report') as $fieldKey => $label) {
            $val = $a[$fieldKey] ?? null;
            if ($val !== null && $val !== '' && strtoupper((string) $val) !== 'NA') {
                $extra[$label] = $val;
            }
        }

        try {
            $report = MedicalReportRequest::create([
                'wa_id'        => $c->wa_id,
                'client_name'  => $a['client_name'] ?? null,
                'patient_name' => $a['patient_name'] ?? null,
                'patient_uid'  => (isset($a['patient_uid']) && strtoupper($a['patient_uid']) !== 'NA') ? $a['patient_uid'] : null,
                'mobile'       => $a['mobile'] ?? null,
                'report_name'  => $a['report_name'] ?? null,
                'extra'        => $extra ?: null,
                'status'       => 'new',
            ]);
            $this->emailReport($report);
        } catch (\Throwable $e) {
            Log::error('WhatsApp medical report save failed: '.$e->getMessage(), ['wa_id' => $c->wa_id]);
            $report = null;
        }

        $c->step = 'idle';
        $data = $c->data ?? [];
        unset($data['report']);
        $c->data = $data;
        $c->save();

        $ref  = $report ? $report->reference() : null;
        $name = $a['client_name'] ?? null;
        $this->wa->sendText(
            $c->wa_id,
            "Thank you".($name ? ', '.strtok($name, ' ') : '')."! 📄\n\n"
            ."We've noted your request".(isset($a['report_name']) ? " for *{$a['report_name']}*" : '')
            ." and our team will contact you shortly to share it."
            .($ref ? "\n🔖 Ref: #{$ref}" : ''),
            $ctx
        );
        $this->backToMenuHint($c, $ctx);
    }

    /** Forward a medical-report request to Customer Care by email. */
    private function emailReport(MedicalReportRequest $r): void
    {
        $adminTo = config('mail.admin_notifications.contact', config('mail.admin_notification'));
        if (! $adminTo) {
            return;
        }

        $rows = [
            'Reference'    => '#'.$r->reference(),
            'Client Name'  => $r->client_name,
            'Patient Name' => $r->patient_name,
            'Patient UID'  => $r->patient_uid,
            'Mobile'       => $r->mobile ?: $r->wa_id,
            'Report'       => $r->report_name,
            'WhatsApp'     => $r->wa_id,
        ];
        foreach ((array) $r->extra as $label => $val) {
            $rows[$label] = $val;
        }

        try {
            Mail::to($adminTo)->send(new WhatsAppNotificationMail(
                'New Medical Report Request',
                'A pet parent has requested a medical report via the WhatsApp assistant. Please share the report.',
                $rows,
                'Please follow up with the pet parent on WhatsApp or by phone.',
                file_exists(public_path('frontend/assets/img/logo/tata-trust-logo.webp'))
            ));
            CommunicationLogger::log([
                'channel' => 'email', 'type' => 'wa_medical_report_admin', 'recipient' => $adminTo,
                'recipient_name' => $r->client_name, 'subject' => 'New Medical Report Request',
                'message' => 'WhatsApp medical report request from '.($r->client_name ?: $r->wa_id), 'status' => 'sent',
            ]);
        } catch (\Throwable $e) {
            Log::error('WhatsApp medical report mail failed: '.$e->getMessage(), ['report_id' => $r->id]);
            CommunicationLogger::log([
                'channel' => 'email', 'type' => 'wa_medical_report_admin', 'recipient' => $adminTo,
                'subject' => 'New Medical Report Request', 'status' => 'failed', 'error' => $e->getMessage(),
            ]);
        }
    }

    /* -------------------------------------------------------------------- */
    /* Shared helpers                                                        */
    /* -------------------------------------------------------------------- */

    /** Send an intro + a previewed link, then offer the menu. */
    private function sendLink(WhatsAppConversation $c, array $ctx, string $intro, string $url): void
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
