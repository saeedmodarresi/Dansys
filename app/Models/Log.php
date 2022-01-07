<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $table = 'logs';
    protected $fillable = ['exchange_id','method','response','type'];
    public $timestamps = true;

    public function exchange()
    {
        return $this->belongsTo(Exchange::class);
    }
}
