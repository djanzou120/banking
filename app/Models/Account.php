<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Account extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'accounts';

    protected $fillable = ['customerId', 'name'];

    public function getSold($accountId)
    {
        return DB::table('accounts')
            ->select(DB::raw('sum(transactions.amount) as sold'))
            ->where('accounts.id', '=', $accountId)
            ->join('transactions', 'accounts.id', '=', 'transactions.accountId')
            ->first();
    }

    public function getCustomerSold($customerId)
    {
        return DB::table('accounts')
            ->select(DB::raw('sum(transactions.amount) as sold'))
            ->where('accounts.customerId', '=', $customerId)
            ->join('transactions', 'accounts.id', '=', 'transactions.accountId')
            ->first();
    }

    public function getCustomerSubSold($customerId)
    {
        return DB::table('accounts')
            ->select('accounts.id', DB::raw('sum(transactions.amount) as sold'))
            ->where('accounts.customerId', '=', $customerId)
            ->join('transactions', 'accounts.id', '=', 'transactions.accountId')
            ->groupBy('accounts.id')
            ->get();
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }

    public function transactions()
    {
        return $this->hasMany('App\Models\Transaction');
    }
}
