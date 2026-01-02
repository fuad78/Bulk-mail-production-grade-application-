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

    public function __construct(Collection $recipients, Campaign $campaign)
    {
        $this->recipients = $recipients;
        $this->campaign = $campaign;
    }

    public function handle(): void
    {
        // Rate Limiting could be applied here using Redis::throttle if needed

        foreach ($this->recipients as $recipient) {
            try {
                $sentMessage = Mail::to($recipient->email)->send(new CampaignEmail($this->campaign, $recipient));

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
