<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
    ];

    /**
     * Helper to check if user is Super Admin
     */
    public function isSuperAdmin(): bool
    {
        return ($this->role === 'superadmin') || ($this->username === 'superadmin');
    }

    /**
     * Helper to check if user is Admin IPDS
     */
    public function isAdminIpds(): bool
    {
        return ($this->role === 'adminipds') || ($this->username === 'adminipds');
    }

    /**
     * Helper to check if user is Admin Sosial
     */
    public function isAdminSosial(): bool
    {
        return ($this->role === 'adminsosial') || ($this->username === 'adminsosial');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
