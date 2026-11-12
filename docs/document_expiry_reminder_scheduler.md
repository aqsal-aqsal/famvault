# 05. Document Expiry Reminders & Task Scheduler

This document covers the automation logic for checking documents with impending expiry dates (vehicle registrations, driver's licenses, passports, etc.).

## 1. Visual Badges in Filament Resource
In `VaultDocumentResource.php`, add remaining-days indicator badge logic to the table column:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('expiry_date')
    ->label('Expiry Date')
    ->date('d M Y')
    ->sortable()
    ->badge()
    ->color(fn ($state) => match (true) {
        empty($state) => 'gray',
        now()->diffInDays($state, false) < 0 => 'danger',   // Already expired
        now()->diffInDays($state, false) <= 30 => 'warning', // Less than 30 days remaining
        default => 'success',                                // Valid / safe
    })
    ->description(function ($record) {
        if (! $record->expiry_date) return null;
        $diff = (int) now()->diffInDays($record->expiry_date, false);
        if ($diff < 0) return abs($diff) . ' days overdue';
        if ($diff === 0) return 'Expires today!';
        return $diff . ' days left';
    }),
```

## 2. Daily Artisan Check Command
Generate the command:
```bash
php artisan make:command CheckDocumentExpiryCommand
```

Implementation in `app/Console/Commands/CheckDocumentExpiryCommand.php`:
```php
namespace App\Console\Commands;

use App\Models\VaultDocument;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CheckDocumentExpiryCommand extends Command
{
    protected $signature = 'vault:check-expiry';
    protected $description = 'Check documents nearing their expiration date';

    public function handle()
    {
        // Documents expiring within the next 30 days
        $expiringDocs = VaultDocument::with('familyMember')
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [now(), now()->addDays(30)])
            ->get();

        if ($expiringDocs->isEmpty()) {
            $this->info('No documents are nearing expiration.');
            return;
        }

        foreach ($expiringDocs as $doc) {
            $owner = $doc->familyMember?->full_name ?? 'General';
            $daysLeft = (int) now()->diffInDays($doc->expiry_date, false);

            $message = "⚠️ *FAMILY DOCUMENT EXPIRY REMINDER*\n\n"
                     . "Document: *{$doc->title}*\n"
                     . "Owner: {$owner}\n"
                     . "Expiry Date: {$doc->expiry_date->format('d-m-Y')}\n"
                     . "Time Remaining: *{$daysLeft} day(s) left*.\nPlease renew promptly!";

            $this->sendNotification($message);
        }
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
```

## 3. Laravel Task Scheduling (`routes/console.php`)
Schedule the command to run automatically every day at 08:00 AM:
```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('vault:check-expiry')->dailyAt('08:00');
```
