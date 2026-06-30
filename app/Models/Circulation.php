<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Circulation extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id', 'user_id', 'borrower_name', 'borrow_date', 'return_date',
        'expected_return_date', 'status', 'purpose', 'notes', 
        'approved_by', 'approved_at', 'return_confirmed_by', 'return_confirmed_at'
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'return_date' => 'date',
        'expected_return_date' => 'date',
        'approved_at' => 'datetime',
        'return_confirmed_at' => 'datetime',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function returnConfirmer()
    {
        return $this->belongsTo(User::class, 'return_confirmed_by');
    }

    // ==================== STATUS ====================
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isReturned()
    {
        return $this->status === 'returned';
    }

    public function isReturnPending()
    {
        return $this->status === 'return_pending';
    }

    public function isActive()
    {
        return in_array($this->status, ['pending', 'approved', 'return_pending']);
    }

    // ==================== APPROVE ====================
    public function approve()
    {
        $this->status = 'approved';
        $this->approved_at = now();
        $this->save();
        
        if ($this->item) {
            $this->item->status = 'borrowed';
            $this->item->save();
        }
    }

    public function reject()
    {
        $this->status = 'rejected';
        $this->save();
    }

    // ==================== RETURN ====================
    public function requestReturn()
    {
        $this->status = 'return_pending';
        $this->save();
    }

    public function confirmReturn($adminId)
    {
        $this->status = 'returned';
        $this->return_date = now();
        $this->return_confirmed_by = $adminId;
        $this->return_confirmed_at = now();
        $this->save();
        
        if ($this->item) {
            $this->item->status = 'available';
            $this->item->save();
        }
    }

    // ==================== SCOPES ====================
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'approved', 'return_pending']);
    }

    public function scopePendingReturn($query)
    {
        return $query->where('status', 'return_pending');
    }
}