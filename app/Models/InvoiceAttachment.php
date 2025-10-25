<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceAttachment extends Model
{
    protected $table = 'invoice_attachment';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'invoice_id',
        'filename',
        'content_type',
        'content',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }
}
