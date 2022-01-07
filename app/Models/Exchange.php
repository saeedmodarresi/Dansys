<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exchange extends Model
{
    use HasFactory;

    protected $table = 'exchanges';
    protected $fillable = ['name', 'url', 'status'];
    public $timestamps = true;

    public function symbols()
    {
        return $this->hasMany(Symbol::class);
    }

    public function logs()
    {
        return $this->hasMany(Log::class);
    }
}
