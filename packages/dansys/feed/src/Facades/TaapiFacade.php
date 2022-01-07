<?php


namespace Dansys\Feed\Facades;

use Illuminate\Support\Facades\Facade;

class TaapiFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'taapi';
    }
}
