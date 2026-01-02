<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

echo "Attempting to send email via [" . Config::get('mail.default') . "]...\n";
echo "Host: " . Config::get('mail.mailers.smtp.host') . "\n";
echo "Port: " . Config::get('mail.mailers.smtp.port') . "\n";
echo "Encryption: " . Config::get('mail.mailers.smtp.encryption') . "\n";
echo "From Name: " . Config::get('mail.from.name') . "\n";
echo "From Address: " . Config::get('mail.from.address') . "\n";

try {
    Mail::raw('This is a test to verify the Email Sending works.', function ($message) {
        $message->to('fuadxeonbd@gmail.com')
            ->subject('System Connectivity Test ' . time());
    });
    echo "Email sent command executed successfully!\n";
} catch (\Exception $e) {
    echo "Error sending email: " . $e->getMessage() . "\n";
    Log::error($e);
}
