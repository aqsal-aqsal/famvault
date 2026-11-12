<?php

namespace App\Console\Commands;

use App\Models\VaultDocument;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CheckDocumentExpiryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vault:check-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check documents nearing their expiration date';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Documents expiring within the next 30 days
        $expiringDocs = VaultDocument::with('familyMember')
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [now(), now()->addDays(30)])
            ->get();

        if ($expiringDocs->isEmpty()) {
            $this->info('No documents are nearing expiration.');
            return Command::SUCCESS;
        }

        foreach ($expiringDocs as $doc) {
            $owner = $doc->familyMember?->full_name ?? 'General';
            $daysLeft = (int) now()->diffInDays($doc->expiry_date, false);

            $message = "⚠️ *FAMILY DOCUMENT EXPIRY REMINDER*\n\n"
                     . "Document: *{$doc->title}*\n"
                     . "Owner: {$owner}\n"
                     . "Expiry Date: {$doc->expiry_date->format('d-m-Y')}\n"
                     . "Time Remaining: *{$daysLeft} day(s) left*.\nPlease renew promptly!";

            $this->line("Found expiring document: {$doc->title} (Owner: {$owner}, Days left: {$daysLeft})");
            $this->sendNotification($message);
        }

        $this->info("Checked {$expiringDocs->count()} expiring document(s).");
        return Command::SUCCESS;
    }

    private function sendNotification(string $text): void
    {
        $botToken = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if ($botToken && $chatId) {
            Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'Markdown',
            ]);
        }
    }
}
