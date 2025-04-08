<?php

namespace App\Providers;

use App\Models\EmailConfiguration;
use App\Models\GeneralSetting;
use App\Models\LogoSetting;
use App\Models\PusherSetting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

    $generalSetting = null;
    $logoSetting = null;
    $mailSetting = null;
    $pusherSetting = null;

    if (Schema::hasTable('general_settings')) {
        $generalSetting = GeneralSetting::first();
    }

    if (Schema::hasTable('logo_settings')) {
        $logoSetting = LogoSetting::first();
    }

    if (Schema::hasTable('email_configurations')) {
        $mailSetting = EmailConfiguration::first();
    }

    if (Schema::hasTable('pusher_settings')) {
        $pusherSetting = PusherSetting::first();
    }

    /** set time zone */
    if ($generalSetting && $generalSetting->time_zone) {
        Config::set('app.timezone', $generalSetting->time_zone);
    }

    /** Set Mail Config */
    if ($mailSetting) {
        Config::set('mail.mailers.smtp.host', $mailSetting->host);
        Config::set('mail.mailers.smtp.port', $mailSetting->port);
        Config::set('mail.mailers.smtp.encryption', $mailSetting->encryption);
        Config::set('mail.mailers.smtp.username', $mailSetting->username);
        Config::set('mail.mailers.smtp.password', $mailSetting->password);
    }

    /** Set Broadcasting Config */
    if ($pusherSetting) {
        Config::set('broadcasting.connections.pusher.key', $pusherSetting->pusher_key);
        Config::set('broadcasting.connections.pusher.secret', $pusherSetting->pusher_secret);
        Config::set('broadcasting.connections.pusher.app_id', $pusherSetting->pusher_app_id);
        Config::set('broadcasting.connections.pusher.options.host', "api-" . $pusherSetting->pusher_cluster . ".pusher.com");
    }

    /** Share variable at all view */
    View::composer('*', function ($view) use ($generalSetting, $logoSetting, $pusherSetting) {
        $view->with([
            'settings' => $generalSetting,
            'logoSetting' => $logoSetting,
            'pusherSetting' => $pusherSetting
        ]);
    });
    }
}
