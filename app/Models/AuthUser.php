<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class AuthUser extends Authenticatable
{
    use Notifiable;

    protected $table = 'auth_user';
    protected $primaryKey = 'id';

    // у тебя UUID, а не auto-increment
    public $incrementing = false;
    protected $keyType = 'string';

    // если resti_auth — отдельное подключение
    protected $connection = 'resti_auth';

    protected $fillable = [
        'email',
        'username',
        'phone',
        'role',
        'status',
    ];

    protected $hidden = [
        // если будешь хранить что-то секретное
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
