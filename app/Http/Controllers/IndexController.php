<?php

    namespace App\Http\Controllers;

    use App\Models\Exchange;
    use App\Models\Log;
    use App\Models\Symbol;
    use Illuminate\Support\Facades\Artisan;
    use phpDocumentor\Reflection\Types\Collection;

    class IndexController
    {

        public function index()
        {
            $results = $logs = '';

            if ( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' ) {

                $results = $this->processData();
                $logs = $this->logs();
            }

            $exchanges = Exchange::query()->select('id', 'name', 'status')->get()->toArray();

            return view('index')->with(compact('exchanges'))->with(compact('results'))->with(compact('logs'));

        }

        public function processData()
        {

            //            Artisan::call("cron:getFeed");

            $output = [];
            $exchanges = Exchange::all()->pluck('name')->toArray();
            $data = Symbol::query()->select('symbols.exchange_id', 'symbols.name as name', 'symbols.last as price')->with('exchange:id,name')->get()->groupBy('name')->toArray();

            foreach ( $data as $key => $value ) {

                if ( preg_match('/[0-9]/m', $key) ) continue;

                $min = collect($value)->pluck('price')->min();
                $max = collect($value)->pluck('price')->max();

                if ( $min and $max ) $output[ $key ][ 'ave' ] = round(( ( $max - $min ) / $min ) * 100, 2);

                foreach ( $value as $item ) {

                    $exchangeName = data_get(data_get($item, 'exchange'), 'name');

                    $output[ $key ]['data'][ $exchangeName ] = data_get($item, 'price');
                }
            }
echo '<pre style="text-align: left; direction: ltr;">';
var_dump($output);
echo '</pre>';
die;
            $result = [];
            foreach ( $output as $key => $value ) {

                if ( data_get($value, 'ave') < 10 ) continue;
                $result[ $key ][] = $value;
            }

            return $result;

        }

        public function logs()
        {
            $msg = [];
            $count = Exchange::query()->where('status', 1)->count();
            $logs = Log::query()->select('exchange_id', 'response', 'type')->with([ 'exchange:id,name' ])->orderBy('created_at', 'desc')->limit($count)->get()->toArray();

            foreach ( $logs as $log ) {

                $msg[] = 'Code ' . $log[ "type" ] . ' ' . $log[ "response" ] . ' in Exchange: ' . $log[ "exchange" ][ "name" ];

            }

            return $msg;
        }

        public function switch()
        {

            $exchangeId = data_get($_POST, 'id');
            $exchangeStatus = data_get($_POST, 'status') == 1 ? 0 : 1;
            $connect = Exchange::query()->where('id', $exchangeId)->update([ 'status' => $exchangeStatus ]);

            if ( !$connect ) return [ 'error' => 'Error in Database, Try again later.' ];

            return [ 'success' => 'Exchange status is changes successfully.' ];
        }
    }
