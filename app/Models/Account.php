<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class Account extends Model
    {

        protected $table = 'accounts';
        protected $fillable = [ 'name', 'key', 'secret', 'token', 'status'];
        public $timestamps = FALSE;

        public static function get( $api,$key )
        {
            return Account::query()->where('key',$key)->where('name', $api)->where('status', 1)->first();
        }

    }
