<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Models\Ministerio;
use Illuminate\Auth\Notifications\VerifyEmail;
use App\Notifications\QueuedVerifyEmail;


class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new QueuedVerifyEmail($url))->toMail($notifiable);
        });

        // View Composer: disponibiliza $ministerios para as views do layout/menu
        View::composer(
            [
                'layouts.app',                    // layout principal
                'components.layouts.navigation',  // <<< aqui: seu componente de menu
                'partials.*'                      // caso use partials
            ],
            function ($view) {
                // carregar ministérios (pode ajustar filtro por usuário se precisar)
                $ministerios = Cache::remember('ministerios_menu_all', 60, fn () => Ministerio::orderBy('nome')->get());
                $view->with('ministerios', $ministerios);
            }
        );
    }
}
