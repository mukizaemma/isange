<?php

namespace App\Providers;

use App\View\Composers\FrontLayoutComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // $this->app->singleton(Excel::class, function ($app) {
        //     return new Excel($app['view'], $app['request']);
        // });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Child views (e.g. frontend.index) are composed under their own name, not
        // layouts.frontbase, so the composer must also match frontend.* or $setting
        // / $about / footer $facilities are undefined in @section('content').
        View::composer(
            ['layouts.frontbase', 'frontend.*'],
            FrontLayoutComposer::class
        );
    }
}
