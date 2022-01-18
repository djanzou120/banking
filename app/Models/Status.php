<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $table = 'status';

    public $timestamps = false;

    public const INIT = 1;
    public const PENDING = 2;
    public const FAILED = 3;
    public const ABORT = 4;
    public const SUCCESS = 5;

    public function transactions()
    {
        return $this->hasMany('App\Models\Transaction');
    }
}
