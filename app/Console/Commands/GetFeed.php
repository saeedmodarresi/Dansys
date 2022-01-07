<?php

namespace App\Console\Commands;


use App\Models\Exchange;
use App\Models\Symbol;
use Illuminate\Console\Command;

class GetFeed extends Command
{
    protected $name = 'cron:getFeed';
    protected $signature = 'cron:getFeed';

    public function handle()
    {

        $timeFrame = '5min';
        Symbol::query()->truncate();
        $exchanges = Exchange::query()->where('status',1)->pluck('name')->toArray();

        foreach ($exchanges as $exchange) {

            $object = "App\\Exchanges\\".$exchange;
            (new $object())->ticker($timeFrame);
        }
    }
}
