<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    protected $fillable = ['createdById', 'firstname', 'lastname', 'phone'];

    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'createdById');
    }

    public function accounts()
    {
        return $this->hasMany('App\Models\Account');
    }
}
