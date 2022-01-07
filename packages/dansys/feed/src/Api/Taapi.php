<?php

    namespace Dansys\Feed\Api;

    use App\Models\Account;
    use App\Models\Log;
    use Dansys\Feed\Interfaces\Api;
    use GuzzleHttp\Client;

    class Taapi implements Api
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

        public function build( $uri, $period, $accountKey )
        {

            //Set period query in url if period is set.(like ema5, ema15, ...)
            $periodQuery = '';
            if ( $period ) {

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

            //Set account secret for build request url
            $account = Account::get('taapi',$accountKey);

            if ( $account ) {

                return [ 'url' => "{$this->baseUrl}{$uri}?secret={$account->secret}{$this->subUrl}{$periodQuery}{$period}" ];

            } else {

                return [ 'error' => 'There is no account available now!' ];
            }
        }

        public function indicator( $uri = '', $period = '', $accountKey = 1 )
        {

            $build = $this->build($uri, $period, $accountKey );

            if ( !data_get($build, 'error') ) {

                $url = data_get($build, 'url');

                try {

                    $data = new Client();
                    $data = $data->get($url)->getBody()->getContents();
                    return json_decode($data, TRUE);

                } catch ( \Exception $e ) {

                    $errorMessage = preg_match('/{(.*.)}/', $e->getMessage(), $matches);
                    $errorMessage = data_get($matches, 0);

                    Log::insert('taapi', "Uri: {$uri}{$period}, Response: {$errorMessage}");

                    return [ 'error' => $e->getMessage() ];
                }
            } else {

                return data_get($build, 'error');
            }
        }
    }
