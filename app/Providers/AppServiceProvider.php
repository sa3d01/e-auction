<?php

namespace App\Providers;

use App\Auction;
use App\Item;
use App\Observers\AuctionObserver;
use App\Observers\ItemObserver;
use App\Observers\UserObserver;
use App\User;
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
        User::observe(UserObserver::class);
        Auction::observe(AuctionObserver::class);
        Item::observe(ItemObserver::class);
    }
}
