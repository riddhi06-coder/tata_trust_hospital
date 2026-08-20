<?php

namespace App\Console\Commands;

use App\Models\CommunicationLog;
use App\Models\WhatsAppConversation;
use App\Support\WhatsAppBot;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Local WhatsApp chatbot tester. Simulates an inbound message and prints the
 * bot's replies — no API credentials, phone, or internet required. The outgoing
 * WhatsApp API call is stubbed with Http::fake() and captured for display.
 *
 * Walk the flow by running it repeatedly (conversation state persists):
 *   php artisan whatsapp:simulate "hi"
 *   php artisan whatsapp:simulate "menu_book"     # simulate tapping a menu row
 *   php artisan whatsapp:simulate "hi" --fresh     # start over
 */
class WhatsAppSimulate extends Command
{
    protected $signature = 'whatsapp:simulate
        {input=hi : Text the user sends, or a menu/button id (e.g. menu_book)}
        {--from=919999900000 : Simulated sender number}
        {--name=Tester : Simulated WhatsApp profile name}
        {--fresh : Reset this conversation before sending}';

    protected $description = 'Locally simulate an inbound WhatsApp message and print the bot replies (no credentials needed).';

    public function handle(WhatsAppBot $bot): int
    {
        $from = (string) $this->option('from');

        if ($this->option('fresh')) {
            WhatsAppConversation::where('wa_id', $from)->delete();
        }

        // Pretend creds exist and stub the outbound call so sends "succeed" + are captured.
        config()->set('services.whatsapp.token', 'SIMULATED');
        config()->set('services.whatsapp.phone_number_id', 'SIMULATED');
        Http::fake(['*' => Http::response(['messages' => [['id' => 'wamid.SIM']]], 200)]);

        $this->newLine();
        $this->line('<fg=cyan;options=bold>USER ▶</> '.$this->argument('input'));

        $bot->handle($from, (string) $this->option('name'), (string) $this->argument('input'));

        foreach (Http::recorded() as [$request]) {
            $this->renderOutgoing(json_decode($request->body(), true) ?? []);
        }

        // The simulated sends leave 'sent' rows in the communication log — clean them up.
        CommunicationLog::where('channel', 'whatsapp')->where('recipient', $from)->delete();

        $convo = WhatsAppConversation::where('wa_id', $from)->first();
        $this->newLine();
        $this->comment('conversation step = '.($convo->step ?? 'idle').'  ·  run again with the next input to continue');

        return self::SUCCESS;
    }

    private function renderOutgoing(array $b): void
    {
        $this->newLine();
        $type = $b['type'] ?? '';

        if ($type === 'text') {
            $this->line('<fg=green;options=bold>BOT  ▶</> '.($b['text']['body'] ?? ''));
            return;
        }

        if ($type === 'image') {
            $img = $b['image'] ?? [];
            $this->line('<fg=green;options=bold>BOT  ▶</> <fg=magenta>[IMAGE]</> '.($img['link'] ?? ''));
            if (! empty($img['caption'])) {
                $this->line('        '.$img['caption']);
            }
            return;
        }

        if ($type === 'interactive') {
            $i = $b['interactive'];
            $this->line('<fg=green;options=bold>BOT  ▶</> '.($i['body']['text'] ?? ''));

            if (($i['type'] ?? '') === 'list') {
                foreach ($i['action']['sections'][0]['rows'] ?? [] as $r) {
                    $desc = isset($r['description']) ? ' — '.$r['description'] : '';
                    $this->line("       <fg=yellow>•</> [{$r['id']}] {$r['title']}{$desc}");
                }
            } elseif (($i['type'] ?? '') === 'button') {
                foreach ($i['action']['buttons'] ?? [] as $btn) {
                    $this->line("       <fg=yellow>[</> {$btn['reply']['id']} <fg=yellow>]</> {$btn['reply']['title']}");
                }
            }
        }
    }
}
