<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;

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
        Gate::before(function ($user, string $ability) {
            if ($user && ($user->role ?? null) === 'admin') {
                return true;
            }

            return null;
        });

        Mail::extend('brevo', function () {
            $config = $this->app['config']->get('services.brevo', []);

            return (new BrevoTransportFactory())->create(
                new Dsn('brevo+api', 'default', $config['key'])
            );
        });
    }
}
