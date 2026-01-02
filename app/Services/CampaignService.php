<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Campaign;
use App\Models\User;
use App\Jobs\SendCampaignJob;
use Illuminate\Support\Facades\DB;

class CampaignService
{
    public function createCampaign(User $user, array $data): Campaign
    {
        return DB::transaction(function () use ($user, $data) {
            $campaign = Campaign::create([
                'user_id' => $user->id,
                'department_id' => $user->department_id,
                'subject' => $data['subject'],
                'body' => $data['body'],
                'sender_id' => $data['sender_id'] ?? null,
                'scheduled_at' => $data['scheduled_at'] ?? null,
                'status' => Campaign::STATUS_DRAFT,
            ]);

            $this->log($user, 'CAMPAIGN_CREATED', "Created campaign {$campaign->id}: {$campaign->subject}");

            return $campaign;
        });
    }

    public function submitForApproval(User $user, Campaign $campaign)
    {
        if ($campaign->status !== Campaign::STATUS_DRAFT) {
            throw new \Exception("Only draft campaigns can be submitted.");
        }

        $campaign->update(['status' => Campaign::STATUS_PENDING_APPROVAL]);
        $this->log($user, 'CAMPAIGN_SUBMITTED', "Submitted campaign {$campaign->id}");
    }

    public function approve(User $approver, Campaign $campaign)
    {
        if (!$approver->isAdmin()) {
            throw new \Exception("Unauthorized");
        }

        // Check Daily Send Limit for the Campaign Creator
        $campaignCreator = $campaign->user;
        if ($campaignCreator->daily_send_limit > 0) {
            // Count emails actually sent by this user today
            $sentToday = \App\Models\Recipient::whereHas('campaign', function ($q) use ($campaignCreator) {
                $q->where('user_id', $campaignCreator->id);
            })->whereDate('sent_at', now())->count();

            $campaignSize = $campaign->recipients()->count();

            // Simple check: existing + new > limit
            // Note: This doesn't account for scheduled future dates perfectly, but prevents approval over limit.
            if (($sentToday + $campaignSize) > $campaignCreator->daily_send_limit) {
                throw new \Exception("Daily send limit exceeded for user {$campaignCreator->name}. Used: $sentToday, Limit: {$campaignCreator->daily_send_limit}, Attempting: $campaignSize");
            }
        }

        $campaign->update(['status' => Campaign::STATUS_APPROVED]);
        $this->log($approver, 'CAMPAIGN_APPROVED', "Approved campaign {$campaign->id}");

        SendCampaignJob::dispatch($campaign);
    }

    public function reject(User $approver, Campaign $campaign, string $reason)
    {
        if (!$approver->isAdmin()) {
            throw new \Exception("Unauthorized");
        }

        $campaign->update(['status' => Campaign::STATUS_REJECTED]);
        $this->log($approver, 'CAMPAIGN_REJECTED', "Rejected campaign {$campaign->id}. Reason: $reason");
    }

    private function log($user, $action, $details)
    {
        AuditLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'details' => $details,
            'ip_address' => request()->ip(),
        ]);
    }

    public function importContactsFromList(Campaign $campaign, int $listId)
    {
        $count = 0;
        \App\Models\Contact::where('contact_list_id', $listId)->chunk(1000, function ($contacts) use ($campaign, &$count) {
            $insertData = [];
            foreach ($contacts as $contact) {
                // Check if email exists in this campaign
                if (\App\Models\Recipient::where('campaign_id', $campaign->id)->where('email', $contact->email)->exists()) {
                    continue;
                }

                $insertData[] = [
                    'campaign_id' => $campaign->id,
                    'email' => $contact->email,
                    'name' => $contact->name,
                    'metadata' => json_encode($contact->metadata), // Assuming metadata is array, cast ensures it's array, json_encode for DB
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($insertData)) {
                \App\Models\Recipient::insert($insertData);
                $count += count($insertData);
            }
        });

        return $count;
    }
}
