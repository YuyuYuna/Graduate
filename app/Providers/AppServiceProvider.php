<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerInertia();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    protected function registerInertia()
    {
        Inertia::version(fn() => md5_file(public_path('js/app.js')));

        Inertia::share([
            'title' => config('app.name'),
            'auth' => fn() => [
                'user' => Auth::user(),
            ],

            'flash' => fn() => [
                'success' => Session::get('success'),
            ]
        ]);
    }
}
