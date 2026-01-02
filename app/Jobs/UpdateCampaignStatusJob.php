<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\Recipient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateCampaignStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaign;
    public $tries = 5;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    public function handle(): void
    {
        // Count pending recipients
        $pendingCount = Recipient::where('campaign_id', $this->campaign->id)
            ->where('status', 'pending')
            ->count();

        if ($pendingCount === 0) {
            // All processed
            $this->campaign->update([
                'status' => Campaign::STATUS_COMPLETED,
                // 'sent_at' => now() // If we tracked overall sent time
            ]);
        } else {
            // Not done yet, release back to queue to check again later
            $this->release(30); // Check again in 30 seconds
        }
    }
}
