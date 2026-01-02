<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->string('name')->nullable();
            $table->json('metadata')->nullable(); // For personalization variables
            // pending, sent, failed, bounced, complained
            $table->string('status')->default('pending');
            $table->string('aws_message_id')->nullable()->index();
            $table->text('error_message')->nullable();
            $table->timestamps();

            // Composite index for fast lookups during sending
            $table->index(['campaign_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipients');
    }
};
