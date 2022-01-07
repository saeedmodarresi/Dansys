<?php


namespace Dansys\Feed;

use Dansys\Feed\Api\Taapi;
use \Illuminate\Support\ServiceProvider;

class DansysServiceProvider extends ServiceProvider
{

    public function register()
    {

        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'feed');

        $this->app->bind('taapi', function () {
            return new Taapi();
        });

//        $this->app->register(Dansys\Feed\DansysServiceProvider::class);
//        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
//        $loader->alias('Taapi',Dansys\Feed\Facades\TaapiFacade::class);
    }

    public function boot()
    {

        $this->publishes([
            __DIR__ . './../config/config.php' => config_path('feed.php')
        ], 'config');

    }
}
