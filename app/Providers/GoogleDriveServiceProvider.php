<?php

namespace App\Providers;

use Google\Client;
use Google\Service\Drive;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class GoogleDriveServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Drive::class, function ($app) {
            $client = new Client();
            $client->setClientId(env('GOOGLE_DRIVE_CLIENT_ID'));
            $client->setClientSecret(env('GOOGLE_DRIVE_CLIENT_SECRET'));
            $client->refreshToken(env('GOOGLE_DRIVE_REFRESH_TOKEN'));
            $client->addScope(Drive::DRIVE);

            Log::info('Google Client initialized', [
                'clientId' => env('GOOGLE_DRIVE_CLIENT_ID'),
                'clientSecret' => env('GOOGLE_DRIVE_CLIENT_SECRET'),
                'refreshToken' => env('GOOGLE_DRIVE_REFRESH_TOKEN')
            ]);

            return new Drive($client);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
