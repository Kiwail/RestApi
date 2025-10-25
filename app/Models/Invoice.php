<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoice';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'client_id',
        'contract_id',
        'number',
        'issued_on',
        'due_on',
        'currency',
        'amount_cents',
        'status',
    ];

    protected $casts = [
        'issued_on'  => 'date',
        'due_on'     => 'date',
        'created_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id', 'id');
    }

    public function attachments()
    {
        return $this->hasMany(InvoiceAttachment::class, 'invoice_id', 'id');
    }
}
