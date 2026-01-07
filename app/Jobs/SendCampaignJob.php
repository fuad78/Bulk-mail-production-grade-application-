<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\Recipient;
use App\Jobs\UpdateCampaignStatusJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;

class SendCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaign;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    public function handle(): void
    {
        $this->campaign->update(['status' => Campaign::STATUS_SENDING]);

        $batchSize = 100;

        // Chunk recipients and dispatch batch jobs
        Recipient::where('campaign_id', $this->campaign->id)
            ->where('status', 'pending')
            ->chunkById($batchSize, function ($recipients) {
                // Dispatch a job for this batch
                SendEmailBatchJob::dispatch($recipients, $this->campaign);
            });

        // Dispatch a follow-up job to check for completion and update status
        UpdateCampaignStatusJob::dispatch($this->campaign)->delay(now()->addMinutes(1));
    }
}
