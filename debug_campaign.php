<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$campaign = \App\Models\Campaign::latest()->first();

if (!$campaign) {
    echo "No campaigns found.\n";
    exit;
}

echo "Campaign: " . $campaign->subject . "\n";
echo "ID: " . $campaign->id . "\n";
echo "Status: " . $campaign->status . "\n";
echo "Sender ID: " . ($campaign->sender_id ?? 'NULL (Default)') . "\n";
echo "Total Recipients: " . $campaign->recipients()->count() . "\n";
echo "Pending: " . $campaign->recipients()->where('status', 'pending')->count() . "\n";
echo "Sent: " . $campaign->recipients()->where('status', 'sent')->count() . "\n";
echo "Failed: " . $campaign->recipients()->where('status', 'failed')->count() . "\n";

if ($campaign->sender) {
    echo "Sender Config: " . json_encode($campaign->sender->configuration) . "\n";
} else {
    echo "Using Default Sender (Env)\n";
}

$lastError = $campaign->recipients()->where('status', 'failed')->latest()->first();
if ($lastError) {
    echo "Last Error: " . $lastError->error_message . "\n";
} else {
    echo "No errors found.\n";
}
