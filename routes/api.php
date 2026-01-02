<?php

use App\Http\Controllers\CampaignController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AwsSesWebhookController;

// Public Webhooks
Route::post('/webhooks/ses', [AwsSesWebhookController::class, 'handle']);

Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Campaigns
    Route::get('/campaigns', [CampaignController::class, 'index']);
    Route::post('/campaigns', [CampaignController::class, 'store']);
    Route::post('/campaigns/{campaign}/upload', [CampaignController::class, 'uploadRecipients']);
    Route::post('/campaigns/{campaign}/submit', [CampaignController::class, 'submit']);

    // Admin Only
    Route::middleware(['role:admin,super_admin'])->group(function () {
        Route::post('/campaigns/{campaign}/approve', [CampaignController::class, 'approve']);
        Route::post('/campaigns/{campaign}/reject', [CampaignController::class, 'reject']);
    });

});
