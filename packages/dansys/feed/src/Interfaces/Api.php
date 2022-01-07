<?php

    namespace Dansys\Feed\Interfaces;

    interface Api
    {
        public function build( $uri, $period, $accountKey );

        public function indicator( $uri, $period );

    }
