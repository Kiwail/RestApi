<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'client';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // у нас только created_at без updated_at

    protected $fillable = [
        'id',
        'auth_user_id',
        'name',
        'email',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function contracts()
    {
        return $this->hasMany(Contract::class, 'client_id', 'id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'client_id', 'id');
    }
}
