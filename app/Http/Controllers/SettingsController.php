<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'app_name' => \App\Models\Setting::get('app_name', config('app.name')),
            'mail_mailer' => \App\Models\Setting::get('mail_mailer', env('MAIL_MAILER')),
            'mail_host' => \App\Models\Setting::get('mail_host', env('MAIL_HOST')),
            'mail_port' => \App\Models\Setting::get('mail_port', env('MAIL_PORT')),
            'mail_username' => \App\Models\Setting::get('mail_username', env('MAIL_USERNAME')),
            // 'mail_password' => env('MAIL_PASSWORD'), // Don't show password
            'mail_encryption' => \App\Models\Setting::get('mail_encryption', env('MAIL_ENCRYPTION')),
            'mail_from_address' => \App\Models\Setting::get('mail_from_address', env('MAIL_FROM_ADDRESS')),

            'aws_access_key_id' => \App\Models\Setting::get('aws_access_key_id', env('AWS_ACCESS_KEY_ID')),
            'aws_default_region' => \App\Models\Setting::get('aws_default_region', env('AWS_DEFAULT_REGION')),
        ];

        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'app_name' => 'required|string|max:50',
            'mail_mailer' => 'required|in:smtp,ses,log',
            'mail_host' => 'required_if:mail_mailer,smtp',
            'mail_port' => 'required_if:mail_mailer,smtp',
            'mail_username' => 'nullable',
            'mail_password' => 'nullable',
            'mail_encryption' => 'nullable|in:tls,ssl,null',
            'mail_from_address' => 'required|email',
            'aws_access_key_id' => 'required_if:mail_mailer,ses',
            'aws_secret_access_key' => 'nullable',
            'aws_default_region' => 'required_if:mail_mailer,ses',
        ]);

        foreach ($validated as $key => $value) {
            if ($value !== null) { // Only update if provided
                if (in_array($key, ['mail_password', 'aws_secret_access_key']) && empty($value)) {
                    continue; // Skip empty password updates
                }
                \App\Models\Setting::set($key, $value);
            }
        }

        return redirect()->route('settings.index')->with('success', 'Settings updated successfully.');
    }
}
