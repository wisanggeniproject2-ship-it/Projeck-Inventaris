<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetDisposal extends Model
{
    use HasFactory;

    protected $fillable = [
    'item_id', 'user_id', 'reason', 'quantity', 'photos', 'status',
    'approved_by', 'approved_at', 'rejection_reason', 'rejected_at',
    ];

    protected $casts = [
    'photos' => 'array',
    'approved_at' => 'datetime',
    'rejected_at' => 'datetime',
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

    // ==================== APPROVE ====================
    public function approve($adminId)
    {
        $this->status = 'approved';
        $this->approved_by = $adminId;
        $this->approved_at = now();
        $this->save();

        // 🔥 Stok dikurangi sesuai jumlah yang diajukan.
        // Kalau stok habis, disposeStock() OTOMATIS set status='disposed' + condition='rusak'.
        // Kalau stok masih ada, item TETAP 'available' + 'baik' biar sisanya bisa dipinjam.
        if ($this->item) {
            $this->item->disposeStock($this->quantity);
        }
    }

    // ==================== REJECT ====================
    public function reject(?string $reason = null)
    {
        $this->status = 'rejected';
        $this->rejected_at = now();
        if ($reason) {
            $this->rejection_reason = $reason;
        }
        $this->save();
    }

    // ==================== SCOPES ====================
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}