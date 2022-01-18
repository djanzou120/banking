<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = ['accountId', 'recipientId', 'statusId', 'amount'];

    public function status()
    {
        return $this->belongsTo('App\Models\Status');
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }
}
