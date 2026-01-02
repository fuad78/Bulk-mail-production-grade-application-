<?php

namespace App\Mail;

use App\Models\Campaign;
use App\Models\Recipient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CampaignEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $campaign;
    public $recipient;

    public function __construct(Campaign $campaign, Recipient $recipient)
    {
        $this->campaign = $campaign;
        $this->recipient = $recipient;
    }

    public function envelope(): Envelope
    {
        $envelope = new Envelope(
            subject: $this->campaign->subject,
        );

        if ($this->campaign->sender) {
            $envelope->from(new Address($this->campaign->sender->email, $this->campaign->sender->name));
        }

        return $envelope;
    }

    public function content(): Content
    {
        return new Content(
            htmlString: $this->replaceVariables($this->campaign->body),
        );
    }

    public function attachments(): array
    {
        return [];
    }

    protected function replaceVariables($content)
    {
        // 1. Basic Metadata Replacement
        $metadata = $this->recipient->metadata ?? [];
        $metadata['name'] = $this->recipient->name;
        $metadata['email'] = $this->recipient->email;

        foreach ($metadata as $key => $value) {
            if (is_string($value)) {
                // Support both {{name}} and {name}
                $content = str_replace("{{" . $key . "}}", $value, $content);
                $content = str_replace("{" . $key . "}", $value, $content);
            }
        }

        // 2. Inject Tracking Pixel
        $trackingUrl = route('tracking.open', ['id' => $this->recipient->id]);
        $pixel = sprintf('<img src="%s" width="1" height="1" alt="" style="display:none;" />', $trackingUrl);

        // Append to body (simple approach, assumes HTML)
        if (strpos($content, '</body>') !== false) {
            $content = str_replace('</body>', $pixel . '</body>', $content);
        } else {
            $content .= $pixel;
        }

        // 3. Rewrite Links for Click Tracking
        // This simple regex looks for href="http..." and wraps it
        // We use a callback to base64 encode the target URL
        $content = preg_replace_callback(
            '/\bhref=["\'](http[^"\']+)["\']/i',
            function ($matches) {
                $originalUrl = $matches[1];
                // Don't rewrite if it's already a tracking link (unlikely but safe)
                if (strpos($originalUrl, url('/t/c/')) !== false) {
                    return $matches[0];
                }

                $encodedUrl = base64_encode($originalUrl);
                $trackingLink = route('tracking.click', [
                    'id' => $this->recipient->id,
                    'target' => $encodedUrl
                ]);

                return 'href="' . $trackingLink . '"';
            },
            $content
        );

        return $content;
    }
}
