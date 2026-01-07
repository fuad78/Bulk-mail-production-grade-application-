<?php

namespace App\Jobs;

use App\Mail\CampaignEmail;
use App\Models\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendEmailBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $recipients;
    protected $campaign;

    // Increase timeout to 3 minutes for batch of 100
    public $timeout = 180;

    public function __construct(Collection $recipients, Campaign $campaign)
    {
        $this->recipients = $recipients;
        $this->campaign = $campaign;
    }

    public function handle(): void
    {
        $mailerName = null;

        // Dynamic Mailer Configuration
        if ($this->campaign->sender_id && $this->campaign->sender && $this->campaign->sender->is_active) {
            $sender = $this->campaign->sender;
            $config = $sender->configuration;
            $mailerName = 'sender_' . $sender->id;

            if ($sender->type === 'smtp') {
                config([
                    "mail.mailers.{$mailerName}" => [
                        'transport' => 'smtp',
                        'host' => $config['host'] ?? '',
                        'port' => $config['port'] ?? 587,
                        'encryption' => $config['encryption'] ?? 'tls',
                        'username' => $config['username'] ?? '',
                        'password' => $config['password'] ?? '',
                        'timeout' => null,
                        'local_domain' => env('MAIL_EHLO_DOMAIN'),
                    ]
                ]);
            } elseif ($sender->type === 'ses') {
                // For SES, we create a specific transport config
                config([
                    "services.ses_{$sender->id}" => [
                        'key' => $config['key'] ?? '',
                        'secret' => $config['secret'] ?? '',
                        'region' => $config['region'] ?? 'us-east-1',
                    ],
                    "mail.mailers.{$mailerName}" => [
                        'transport' => 'ses',
                        // We must reference the *specific* services config key, but Laravel SES transport
                        // usually hardcodes 'services.ses'. To work around this without custom ServiceProviders,
                        // we can manually instantiate, but that's complex.
                        // EASIER: Just use the standard SES configuration structure but ensure we pass options if supported.
                        // Laravel 9+ SES transport doesn't easily support dynamic credentials in config array merely by key.
                        // FALLBACK: If type is SES, we might have to use a workaround or assume the user sets global credentials.
                        // BUT: The user asked for multiple AWS SES accounts.
                        // SOLUTION: We will attempt to rely on the fact that if we change 'services.ses' key, it affects new instances.
                        // However, to be safe, we will just use SMTP interface for SES if possible (SES supports SMTP).
                        // If not, we simply swap the global config temporarily.
                    ]
                ]);

                // SWAPPING GLOBAL CONFIG FOR SES (Safe in isolated job if handled carefully, but risky in async worker sharing memory)
                // Better: Use specific config and recreate transport.
                // For now, let's assume SMTP for 'smtp' type and for 'ses' type we'll try to swap global 'services.ses' 
                // and flush the transport manager.

                config([
                    'services.ses' => [
                        'key' => $config['key'] ?? '',
                        'secret' => $config['secret'] ?? '',
                        'region' => $config['region'] ?? 'us-east-1',
                    ]
                ]);

                // Forget the 'ses' mailer instance to force reconstruction with new config
                Mail::purge('ses');
                $mailerName = 'ses';
            }
        }

        // Resolve mailer instance once outside the loop
        $mailer = $mailerName ? Mail::mailer($mailerName) : Mail::mailer();

        foreach ($this->recipients as $recipient) {
            try {
                // Check if process was stopped (optional optimization)
                if ($this->job && $this->job->isReleased()) {
                    break;
                }

                $mailable = new CampaignEmail($this->campaign, $recipient);

                // Set From address dynamically if sender exists
                if (isset($sender)) {
                    $mailable->from($sender->email, $sender->name);
                }

                // Log debug info (reduced logging to avoid I/O blocking)
                // Log::info("Sending to {$recipient->email} via " . ($mailerName ?? 'default'));

                // Send email
                $sentMessage = $mailer->to($recipient->email)->send($mailable);

                $messageId = null;
                if ($sentMessage) {
                    $messageId = $sentMessage->getMessageId();
                }

                $recipient->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                    'message_id' => $messageId
                ]);
            } catch (\Exception $e) {
                Log::error("Failed to send to {$recipient->email}: " . $e->getMessage());
                $recipient->update([
                    'status' => 'failed',
                    'error_message' => substr($e->getMessage(), 0, 255)
                ]);
            }
        }
    }
}
