<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantRegistry extends Model
{
    protected $table = 'tenant_registry';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $connection = 'master';

    protected $fillable = [
        'id',
        'company_id',
        'db_name',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
}
