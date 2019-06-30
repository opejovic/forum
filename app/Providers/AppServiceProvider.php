<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \View::composer('*', function($view) {
            $channels = \Cache::rememberForever('channels', function () {
                return \App\Models\Channel::all();
            });

            $view->with('channels', $channels);
        });

        \Gate::before(function ($user) {
            if ($user->email === 'john@example.com') {
                return true;
            }
        });
    }
}
