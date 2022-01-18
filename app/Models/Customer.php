<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    protected $fillable = ['createdBy', 'firstname', 'lastname', 'phone'];

    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'createdBy');
    }

    public function accounts()
    {
        return $this->hasMany('App\Models\Account');
    }
}
