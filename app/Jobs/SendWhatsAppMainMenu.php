<?php

namespace App\Jobs;

use App\Support\WhatsAppBot;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Re-shows the WhatsApp main menu to a user a short while after they finished a
 * booking (dispatched with a delay from WhatsAppBot::finaliseBooking), so the
 * booking confirmation + link land first.
 *
 * The real delay needs a queue worker (database/redis driver). With the sync
 * driver the menu is sent immediately.
 */
class SendWhatsAppMainMenu implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $waId) {}

    public function handle(WhatsAppBot $bot): void
    {
        $bot->sendMainMenu($this->waId);
    }
}
