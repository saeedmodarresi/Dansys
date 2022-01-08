<?php

    namespace App\Http\Controllers;

    use App\Models\Exchange;
    use App\Models\Log;
    use App\Models\Symbol;
    use Illuminate\Support\Facades\Artisan;

    /*
    |--------------------------------------------------------------------------
    | Main Controller for UI
    |--------------------------------------------------------------------------
    */

    class IndexController
    {

        public function index()
        {
            $data = $logs = [];

            if ( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' ) {

                $this->getData();
                $data = $this->processData();
                $logs = $this->logs();

                /*
                |--------------------------------------------------------------------------
                | Filter output by percent of average
                |--------------------------------------------------------------------------
                */
                foreach ($data as $key => $value){
                    if(data_get($value,'ave') < 10) unset($data[$key]);
                }
            }

            $exchanges = Exchange::query()->select('id', 'name', 'status')->get()->toArray();
            return view('index')
                ->with(compact('exchanges'))
                ->with(compact('logs'))
                ->with(compact('data'));

        }

        /*
        |--------------------------------------------------------------------------
        | Command for get data from exchanges api
        |--------------------------------------------------------------------------
        */
        public function getData() : void
        {
            Artisan::call("cron:getFeed");
        }

        /*
        |--------------------------------------------------------------------------
        | Initialize data for show
        |--------------------------------------------------------------------------
        */
        public function processData()
        {

            $output = [];
            $exchanges = Exchange::all()->pluck('name')->toArray();
            $data = Symbol::query()->select('symbols.exchange_id', 'symbols.name as name', 'symbols.last as price')->with('exchange:id,name')->get()->groupBy('name')->toArray();

            $i = 0;
            foreach ( $data as $key => $value ) {

                $output[ $i ][ 'name' ] = $key;

                /*
                |--------------------------------------------------------------------------
                | Remove symbols that use number in it's name(Not my goal)
                |--------------------------------------------------------------------------
                */
                if ( preg_match('/[0-9]/m', $key) ) continue;

                /*
                |--------------------------------------------------------------------------
                | Find min and max price for a symbol and get average from them
                |--------------------------------------------------------------------------
                */
                $min = collect($value)->pluck('price')->min();
                $max = collect($value)->pluck('price')->max();
                $ave = ( $min and $max ) ? round(( ( $max - $min ) / $min ) * 100, 2) : '';
                $output[ $i ][ 'ave' ] = $ave;

                /*
                |--------------------------------------------------------------------------
                | Set all exchange into data for similarization
                |--------------------------------------------------------------------------
                */
                foreach ( $exchanges as $key2 => $value2 ) {
                    $output[ $i ][ 'exchanges' ][ $value2 ] = '';
                }

                /*
                |--------------------------------------------------------------------------
                | Fill true exist data to related exchanges
                |--------------------------------------------------------------------------
                */
                foreach ( $value as $item ) {

                    $exchangeName = data_get(data_get($item, 'exchange'), 'name');

                    $output[ $i ][ 'exchanges' ][ $exchangeName ] = data_get($item, 'price');
                }

                $i++;
            }

            return $output;
        }

        /*
        |--------------------------------------------------------------------------
        | Get log for each api is sending
        |--------------------------------------------------------------------------
        */
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

        /*
        |--------------------------------------------------------------------------
        | Switch for select exchanges or deselect them in get data
        |--------------------------------------------------------------------------
        */
        public function switch()
        {

            $exchangeId = data_get($_POST, 'id');
            $exchangeStatus = data_get($_POST, 'status') == 1 ? 0 : 1;
            $connect = Exchange::query()->where('id', $exchangeId)->update([ 'status' => $exchangeStatus ]);

            if ( !$connect ) return [ 'error' => 'Error in Database, Try again later.' ];

            return [ 'success' => 'Exchange status is changes successfully.' ];
        }
    }
