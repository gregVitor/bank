<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'account';

    protected $fillable = [
        'user_id', 'type', 'amount', 'operation'
    ];

    /**
     * Retorna o usuario do extrato.
     */
    public function accountUser()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
