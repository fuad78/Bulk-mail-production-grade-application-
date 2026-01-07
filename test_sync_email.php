<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting Sync Email Test...\n";

try {
    Mail::raw('This is a DIRECT SYNC test email.', function ($message) {
        $message->to('fuelbd77@gmail.com') // Replace with a valid email you have access to, or ask user? I'll use a generic one or try to pick one from logs? 
            ->subject('Direct Sync Test Email');
    });
    echo "Email Sent Successfully (Sync)!\n";
} catch (\Exception $e) {
    echo "Email Failed: " . $e->getMessage() . "\n";
    Log::error("Sync Email Test Failed: " . $e->getMessage());
}
