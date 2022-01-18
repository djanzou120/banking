<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'accounts';

    protected $fillable = ['customerId', 'name'];

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }

    public function transactions()
    {
        return $this->hasMany('App\Models\Transaction');
    }
}
