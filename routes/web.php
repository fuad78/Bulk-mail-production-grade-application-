<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CampaignController;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
});

// Auth Routes
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'create'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'store']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'destroy'])->name('logout');

// Tracking Routes (Public)
Route::get('/t/o/{id}', [App\Http\Controllers\TrackingController::class, 'trackOpen'])->name('tracking.open');
Route::get('/t/c/{id}', [App\Http\Controllers\TrackingController::class, 'trackClick'])->name('tracking.click');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // UI Routes
    Route::get('/campaigns', [CampaignController::class, 'indexView'])->name('campaigns.index');
    Route::get('/campaigns/create', [CampaignController::class, 'create'])->name('campaigns.create');
    Route::post('/campaigns', [CampaignController::class, 'storeWeb'])->name('campaigns.store');
    Route::get('/campaigns/{campaign}', [CampaignController::class, 'show'])->name('campaigns.show');

    // Campaign Actions (Web)
    Route::post('/campaigns/{campaign}/upload', [CampaignController::class, 'uploadRecipientsWeb'])->name('campaigns.upload');
    Route::post('/campaigns/{campaign}/import-list', [CampaignController::class, 'importList'])->name('campaigns.import_list');
    Route::post('/campaigns/{campaign}/submit', [CampaignController::class, 'submitWeb'])->name('campaigns.submit');
    Route::post('/campaigns/{campaign}/approve', [CampaignController::class, 'approveWeb'])->name('campaigns.approve');
    Route::post('/campaigns/{campaign}/reject', [CampaignController::class, 'rejectWeb'])->name('campaigns.reject');
    Route::post('/campaigns/{campaign}/retry', [CampaignController::class, 'retryFailed'])->name('campaigns.retry');
    Route::delete('/campaigns/{campaign}', [CampaignController::class, 'destroyWeb'])->name('campaigns.destroy');

    Route::resource('/admin/users', App\Http\Controllers\UserController::class)->names([
        'index' => 'users.index',
        'create' => 'users.create',
        'store' => 'users.store',
        'show' => 'users.show',
        'edit' => 'users.edit',
        'update' => 'users.update',
        'destroy' => 'users.destroy',
    ]);
    Route::resource('/admin/departments', App\Http\Controllers\DepartmentController::class)->names([
        'index' => 'departments.index',
        'create' => 'departments.create',
        'store' => 'departments.store',
        'show' => 'departments.show',
        'edit' => 'departments.edit',
        'update' => 'departments.update',
        'destroy' => 'departments.destroy',
    ]);
    Route::get('/admin/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::resource('/admin/senders', App\Http\Controllers\SenderController::class)->names([
        'index' => 'senders.index',
        'create' => 'senders.create',
        'store' => 'senders.store',
        'show' => 'senders.show',
        'edit' => 'senders.edit',
        'update' => 'senders.update',
        'destroy' => 'senders.destroy',
    ]);

    // Address Book / Contact Lists
    Route::resource('/lists', App\Http\Controllers\ContactListController::class);

    // Logs & Calendar
    Route::get('/admin/audit-logs', [App\Http\Controllers\AuditLogController::class, 'index'])->name('admin.audit.index');
    Route::get('/admin/audit-logs/export', [App\Http\Controllers\AuditLogController::class, 'export'])->name('admin.audit.export');
    Route::get('/admin/access-control', function () {
        return view('admin.access.index');
    })->name('admin.access.index');
    Route::get('/calendar', [App\Http\Controllers\CalendarController::class, 'index'])->name('calendar.index');

    Route::post('/admin/settings', [App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');

    // Profile
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/password', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/info', [App\Http\Controllers\ProfileController::class, 'updateInfo'])->name('profile.info');

    // Campaign Routes (API)
    Route::prefix('api/campaigns')->controller(CampaignController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::post('/{campaign}/upload-recipients', 'uploadRecipients');
        Route::post('/{campaign}/submit', 'submit');
        Route::post('/{campaign}/approve', 'approve');
        Route::post('/{campaign}/reject', 'reject');
    });
});
