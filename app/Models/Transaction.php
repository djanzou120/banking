<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_INIT = 'INIT';
    public const STATUS_SUCCESS = 'SUCCESS';

    public const TYPE_DEPOSIT = 'DEPOSIT';
    public const TYPE_SEND = 'SEND';
    public const TYPE_RECEIVE = 'RECEIVE';

    protected $table = 'transactions';

    protected $fillable = ['accountId', 'recipientId', 'amount', 'status', 'type', 'fromId', 'depositAgentId'];

    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

    public function depositAgent()
    {
        return $this->belongsTo('App\Models\User', 'depositAgentId');
    }

    public function recipient()
    {
        return $this->belongsTo('App\Models\User', 'recipientId');
    }
}
