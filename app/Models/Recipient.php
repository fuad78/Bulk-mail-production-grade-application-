<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'email',
        'name',
        'metadata',
        'status',
        'sent_at',
        'aws_message_id', // keeping valid for legacy/migration 2025
        'message_id',     // new column from migration 2026
        'error_message',
        'opened_at',
        'clicked_at',
        'bounced_at',
        'complained_at',
        'bounce_type',
        'complaint_type',
    ];

    protected $casts = [
        'metadata' => 'array',
        'sent_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
        'bounced_at' => 'datetime',
        'complained_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
