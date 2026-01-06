<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class AuthUser extends Authenticatable
{
    use Notifiable;

    protected $table = 'auth_user';
    protected $primaryKey = 'id';

    // tev ir UUID, nevis auto-increment
    public $incrementing = false;
    protected $keyType = 'string';

    // ja resti_auth — atsevišķs savienojums
    protected $connection = 'resti_auth';

    protected $fillable = [
        'email',
        'username',
        'phone',
        'role',
        'status',
    ];

    protected $hidden = [
        // ja glabāsi kaut ko slepenu
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
