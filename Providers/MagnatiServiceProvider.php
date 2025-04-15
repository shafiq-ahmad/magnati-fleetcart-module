<?php

namespace Modules\Magnati\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Admin\Ui\Facades\TabManager;
use Modules\Payment\Facades\Gateway;
use Modules\Magnati\Admin\MagnatiTabs;
use Modules\Magnati\Gateways\Magnati;

class MagnatiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        
        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'magnati');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'magnati');
        
        $this->registerMagnatiGateway();
        $this->registerMagnatiTabs();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../Config/config.php', 'magnati');
    }

    /**
     * Register the Magnati gateway.
     *
     * @return void
     */
    private function registerMagnatiGateway()
    {
        Gateway::register('magnati', function () {
            return new Magnati();
        });
    }
    
    /**
     * Register Magnati tabs for the admin settings.
     *
     * @return void
     */
    private function registerMagnatiTabs()
    {
        if (! TabManager::has('payment_methods')) {
            return;
        }

        TabManager::register('payment_methods', function ($tabs) {
            $tabs->group('payment_methods')
                ->add('magnati', function ($tab) {
                    $tab->weight(20)
                        ->view('magnati::admin.magnati.settings')
                        ->variables([
                            'tabs' => TabManager::get('magnati_settings'),
                        ]);
                });
        });

        TabManager::register('magnati_settings', function ($tabs) {
            return new MagnatiTabs($tabs);
        });
    }
}
