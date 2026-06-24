<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'unit_id', 'phone', 'is_active'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function circulations()
    {
        return $this->hasMany(Circulation::class);
    }

    public function approvedCirculations()
    {
        return $this->hasMany(Circulation::class, 'approved_by');
    }

    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function isAdminUnit()
    {
        return $this->role === 'admin_unit';
    }

    public function isManager()
    {
        return $this->role === 'manager';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }
}