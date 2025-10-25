<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $table = 'contract';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'client_id',
        'number',
        'status',
        'signed_at',
    ];

    protected $casts = [
        'signed_at'  => 'date',
        'created_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'contract_id', 'id');
    }
}
