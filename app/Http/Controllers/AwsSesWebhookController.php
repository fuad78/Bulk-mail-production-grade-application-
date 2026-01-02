<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class AwsSesWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // AWS Sends JSON in the body
        $payload = json_decode($request->getContent(), true);

        if (!$payload) {
            return response('Invalid Payload', 400);
        }

        // Handle SNS Subscription Confirmation
        if (isset($payload['Type']) && $payload['Type'] === 'SubscriptionConfirmation') {
            Log::info('Confirming AWS SNS Subscription for SES');
            Http::get($payload['SubscribeURL']);
            return response('Confirmed', 200);
        }

        // Handle Notifications (Bounce, Complaint, Delivery)
        if (isset($payload['Type']) && $payload['Type'] === 'Notification') {
            $message = json_decode($payload['Message'], true);

            if (!$message) {
                return response('Invalid Message JSON', 400);
            }

            $notificationType = $message['notificationType'] ?? null;
            $mailCommonHeaders = $message['mail']['messageId'] ?? null; // AWS SES Message ID

            if (!$mailCommonHeaders) {
                // If messageId is not found directly, check headers or other fields?
                // SES usually provides messageId in $message['mail']['messageId']
                Log::warning('SES Webhook: No Message ID found', $message);
                return response('No Message ID', 200);
            }

            $messageId = $message['mail']['messageId'];

            // Find Recipient by Message ID
            $recipient = Recipient::where('message_id', $messageId)->first();

            if (!$recipient) {
                // It might be a test email or from a different system
                Log::info("SES Webhook: Recipient not found for Message ID: $messageId");
                return response('Recipient not found', 200);
            }

            if ($notificationType === 'Bounce') {
                $bounceType = $message['bounce']['bounceType'] ?? 'Unknown';
                $recipient->update([
                    'bounced_at' => now(),
                    'bounce_type' => $bounceType
                ]);
                Log::info("SES Bounce recorded for recipient: {$recipient->id}, Type: $bounceType");
            } elseif ($notificationType === 'Complaint') {
                $complaintType = $message['complaint']['complaintFeedbackType'] ?? 'Unknown';
                $recipient->update([
                    'complained_at' => now(),
                    'complaint_type' => $complaintType
                ]);
                Log::info("SES Complaint recorded for recipient: {$recipient->id}, Type: $complaintType");
            } elseif ($notificationType === 'Delivery') {
                // Optional: Update status to 'delivered' if we want detailed tracking
                // $recipient->update(['status' => 'delivered']);
            }
        }

        return response('Processed', 200);
    }
}
