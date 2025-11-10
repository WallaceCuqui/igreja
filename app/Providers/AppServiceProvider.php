<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event; // ✅ IMPORTANTE
use Illuminate\Auth\Events\Login;     // ✅ IMPORTANTE
use App\Models\Ministerio;
use Illuminate\Auth\Notifications\VerifyEmail;
use App\Notifications\QueuedVerifyEmail;
use App\Listeners\AtualizarAgendasAposLogin;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ✅ Envio de e-mail de verificação personalizado (em fila)
        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new QueuedVerifyEmail($url))->toMail($notifiable);
        });

        // ✅ Atualiza agendas automaticamente quando um usuário loga
        Event::listen(Login::class, [AtualizarAgendasAposLogin::class, 'handle']);

        // ✅ View Composer: disponibiliza $ministerios para os layouts e menus
        View::composer(
            [
                'layouts.app',                    // layout principal
                'components.layouts.navigation',  // componente de menu
                'partials.*'                      // se usar partials
            ],
            function ($view) {
                $ministerios = Cache::remember(
                    'ministerios_menu_all',
                    60,
                    fn () => Ministerio::orderBy('nome')->get()
                );

                $view->with('ministerios', $ministerios);
            }
        );
    }
}
