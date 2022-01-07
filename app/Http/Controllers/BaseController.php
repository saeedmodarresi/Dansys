<?php

namespace App\Http\Controllers;

use App\Models\Exchange;
use App\Models\Log;
use App\Models\Symbol;
use GuzzleHttp\Client;

class BaseController
{
    protected $exchangeId = '';

    public function __construct()
    {

        preg_match('/[^\\\\]+$/i', get_class($this), $match);
        $exchange = Exchange::query()->where('name',$match)->first();
        $this->exchangeId = $exchange->id;
    }

    public function sendRequest($exchange_id, $method)
    {

        $url = Exchange::find($exchange_id)->url;

        try {

            $client = new Client();
            $data = $client->get($url);
            $data = $data->getBody()->getContents();

            $this->insertLog([
                'exchange_id' => $exchange_id,
                'method'      => $method,
                'response'    => "Get data is success",
                'type'        => '200'
            ]);

            return json_decode($data);

        } catch (\Exception $e) {

            $params = [
                'exchange_id' => $exchange_id,
                'method'      => $method,
                'response'    => data_get(explode(' response:', $e->getMessage()), 0),
                'type'        => $e->getCode()
            ];

            return $this->insertLog($params);
        }
    }

    public function insertLog($params): void
    {

        Log::query()->create([
            'exchange_id' => data_get($params, 'exchange_id'),
            'method'      => data_get($params, 'method'),
            'response'    => data_get($params, 'response'),
            'type'        => data_get($params, 'type'),
        ]);
    }

    public function insertSymbol($params): void
    {
        Symbol::query()->create([
            'exchange_id' => data_get($params, 'exchange_id'),
            'name'        => '',
            'last'        => data_get($params, 'last'),
            'time_frame'  => data_get($params, 'time_frame'),
            'created_at'  => now(),
            'updated_at'  => now()
        ]);

    }

    public function checkSymbol($name)
    {

        if (!strpos(strtolower($name), 'usdt')) return [];
        $pattern = '/[\W_(^)]*usdt$/mi';
        return strtolower(preg_replace($pattern, '-USDT', $name));
    }


    public function queryToSql($query)
    {
        return vsprintf(str_replace('?', '%s', $query->toSql()), collect($query->getBindings())->map(function ($binding) {
            $binding = addslashes($binding);
            return is_numeric($binding) ? $binding : "'{$binding}'";
        })->toArray());
    }

}
