<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Symbol extends Model
{
    use HasFactory;

    protected $table = 'symbols';
    protected $guarded = ['id'];
    public $timestamps = true;

    public function exchange()
    {
        return $this->belongsTo(Exchange::class);
    }
}
