<?php

namespace App\Providers;

use App\Events\BershkaProductRequest;
use App\Events\DefactoProductRequest;
use App\Events\MangoProductRequest;
use App\Listeners\MangoProductRequestHandler;
use App\Listeners\TrendyolProductRequestHandler;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen(
            BershkaProductRequest::class,
            [TrendyolProductRequestHandler::class, 'handle'],
            
        );
        Event::listen(DefactoProductRequest::class,[
            TrendyolProductRequestHandler::class, 'handle'
        ]);
        Event::listen(MangoProductRequest::class,[
            MangoProductRequestHandler::class, "handle"
        ]);
    }
}
