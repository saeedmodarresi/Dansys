<?php

    namespace Dansys\Feed\Api;

    use App\Models\Account;
    use Dansys\Feed\Interfaces\Api;
    use GuzzleHttp\Client;

    class Coinmarketcap implements Api
    {
        private $baseUrl;
        private $subUrl;
        private $symbol;
        private $exchange;
        private $interval;
        private $secret;

        public function __construct()
        {
            $this->exchange = config('feed.config.exchange');
            $this->symbol = config('feed.config.symbol');
            $this->interval = config('feed.config.timeframe');
            $this->baseUrl = config('feed.api.taapi.baseurl');
            $this->secret = config('feed.api.taapi.secretkeys');
            $this->subUrl = "&exchange={$this->exchange}&symbol={$this->symbol}&interval={$this->interval}&backtrack=1";

        }

        public function build( $uri, $period )
        {
            //Get an active account from taapi accounts
            $account = Account::get('taapi');
            $periodQuery = '';

            //Set period query in url if period is set.(like ema5, ema15, ...)
            if ($period){

                switch ( $uri ) {
                    case 'ema':
                        $periodQuery = '&optInTimePeriod=';
                        break;
                    case 'ma':
                        $periodQuery = '&optInTimePeriod=';
                        break;
                    case 'bbands2':
                        $periodQuery = '&period=';
                        break;
                    case 'stoch':
                        $periodQuery = '&kPeriod=';
                        break;
                }
                $periodQuery = !empty($periodQuery) ? $periodQuery : $periodQuery = '';
            }

            if ( $account ) {

                return "{$this->baseUrl}{$uri}?secret={$account->secret}{$this->subUrl}{$periodQuery}{$period}";

            } else {

                return [ 'There is no account available now!' ];
            }
        }

        public function indicator( $uri = '', $period = '' )
        {

            $url = $this->build($uri, $period);

            try{

                $data = new Client();
                $data = $data->get($url)->getBody()->getContents();
                return json_decode($data, TRUE);

            }catch (\Exception $e){

                return $e->getMessage();
            }

        }
    }
