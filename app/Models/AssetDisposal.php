<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetDisposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'item_stock_id',   // 🔥 TAMBAH — FK ke item_stocks
        'stock_code',      // 🔥 TAMBAH — kode stok spesifik yang diajukan
        'user_id',
        'reason',
        'quantity',
        'photos',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'rejected_at',
    ];

    protected $casts = [
        'photos'        => 'array',
        'approved_at'   => 'datetime',
        'rejected_at'   => 'datetime',
        'quantity'      => 'integer',
    ];

    // ==================== RELATIONS ====================
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * 🔥 Relasi ke kode stok spesifik yang diajukan
     */
    public function itemStock()
    {
        return $this->belongsTo(ItemStock::class, 'item_stock_id');
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
        // Update status disposal
        $this->status      = 'approved';
        $this->approved_by = $adminId;
        $this->approved_at = now();
        $this->save();

        // 🔥 1. Update status item_stock → disposed (kalau ada)
        if ($this->item_stock_id && $this->itemStock) {
            $this->itemStock->update([
                'status' => 'disposed',
                'notes'  => 'Dihapus via pengajuan #' . $this->id . ' (' . ($this->reason ?? '-') . ')',
            ]);
        }

        // 🔥 2. Kurangi stok item & tambah disposed_stock
        if ($this->item) {
            $this->item->disposeStock($this->quantity);
        }
    }

    // ==================== REJECT ====================
    public function reject(?string $reason = null)
    {
        $this->status      = 'rejected';
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

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}