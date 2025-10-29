<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'company';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $connection = 'master';

    protected $fillable = [
        'id',
        'name',
        'slug',
        'status',
    ];

    public function apiKeys()
    {
        return $this->hasMany(ApiKey::class, 'company_id', 'id');
    }

    public function tenantRegistry()
    {
        return $this->hasOne(TenantRegistry::class, 'company_id', 'id');
    }
}
