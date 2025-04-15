<?php

namespace Modules\Magnati\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Modules\Magnati\Events\MagnatiPaymentCompleted;
use Modules\Magnati\Listeners\HandlePaymentCompleted;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerEventListeners();
    }

    /**
     * Register event listeners.
     *
     * @return void
     */
    private function registerEventListeners()
    {
        Event::listen(MagnatiPaymentCompleted::class, HandlePaymentCompleted::class);
    }
}
