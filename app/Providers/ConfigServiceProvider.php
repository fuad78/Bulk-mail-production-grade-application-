<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Models\Setting;

class ConfigServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        try {
            if (Schema::hasTable('settings')) {
                $appName = Setting::get('app_name');
                if ($appName) {
                    Config::set('app.name', $appName);
                    Config::set('mail.from.name', $appName);
                }

                $mailer = Setting::get('mail_mailer');
                if ($mailer) {
                    Config::set('mail.default', $mailer);

                    if ($mailer === 'smtp') {
                        Config::set('mail.mailers.smtp.host', Setting::get('mail_host'));
                        Config::set('mail.mailers.smtp.port', Setting::get('mail_port'));
                        Config::set('mail.mailers.smtp.username', Setting::get('mail_username'));
                        Config::set('mail.mailers.smtp.password', Setting::get('mail_password'));
                        Config::set('mail.mailers.smtp.encryption', Setting::get('mail_encryption'));
                    } elseif ($mailer === 'ses') {
                        Config::set('services.ses.key', Setting::get('aws_access_key_id'));
                        Config::set('services.ses.secret', Setting::get('aws_secret_access_key'));
                        Config::set('services.ses.region', Setting::get('aws_default_region'));
                    }

                    Config::set('mail.from.address', Setting::get('mail_from_address'));
                }
            }
        } catch (\Exception $e) {
            // Squelch errors during boot (e.g. migration not run yet, cache table missing)
            // ensuring artisan commands don't crash.
        }
    }
}
