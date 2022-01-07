<?php


return [

    /*
    |--------------------------------------------------------------------------
    | General settings
    |--------------------------------------------------------------------------
    */
    'config' => [
        'exchange'    => 'binance',
        'symbol'      => 'BTC/USDT',
        'timeframe'   => '1h',
    ],

    'api'  => [
        /*
        |--------------------------------------------------------------------------
        | Taapi
        |--------------------------------------------------------------------------
        */
        'taapi' => [
            'baseurl'    => 'https://api.taapi.io/',
        ]
    ]
];