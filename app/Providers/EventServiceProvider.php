<?php

namespace App\Providers;

use App\Events\AuctionLive;
use App\Events\AuctionNegotiation;
use App\Events\AuctionTimeOut;
use App\Listeners\NotifyAuctionLive;
use App\Listeners\NotifyAuctionNegotiation;
use App\Listeners\NotifyAuctionTimeOut;
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
        AuctionTimeOut::class => [
            NotifyAuctionTimeOut::class,
        ],
        AuctionLive::class => [
            NotifyAuctionLive::class,
        ],
        AuctionNegotiation::class => [
            NotifyAuctionNegotiation::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
