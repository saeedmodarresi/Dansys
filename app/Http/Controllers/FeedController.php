<?php

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use Taapi;

    class FeedController extends Controller
    {
        public function index()
        {

            $a = Taapi::indicator('macd','',1);
            $b = Taapi::indicator('ema',5,2);
            $c = Taapi::indicator('ema',10,3);
            $d = Taapi::indicator('ema',20,4);
            $e = Taapi::indicator('bbands',7);
            $f = Taapi::indicator('rsi',8);

            echo '<pre style="text-align: left; direction: ltr;">';
            var_dump($a);
            var_dump($b);
            var_dump($c);
            var_dump($d);
            var_dump($e);
            var_dump($f);
            echo '</pre>';
            die;
        }
    }
