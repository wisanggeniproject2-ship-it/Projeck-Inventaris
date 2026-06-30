<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'circulation_id', 'title', 'message', 'type', 'status', 'read_at'
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function circulation()
    {
        return $this->belongsTo(Circulation::class);
    }

    // ==================== SCOPES ====================
    public function scopeUnread($query)
    {
        return $query->where('status', 'unread');
    }

    public function scopeRead($query)
    {
        return $query->where('status', 'read');
    }

    // ==================== METHODS ====================
    public function markAsRead()
    {
        $this->status = 'read';
        $this->read_at = now();
        $this->save();
    }

    public function isUnread()
    {
        return $this->status === 'unread';
    }
}