<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Circulation extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'item_stock_id',      // 🔥 TAMBAH — FK ke item_stocks
        'stock_code',         // 🔥 TAMBAH — kode stok spesifik
        'user_id',
        'borrower_name',
        'borrow_date',
        'return_date',
        'expected_return_date',
        'status',
        'purpose',
        'notes',
        'approved_by',
        'approved_at',
        'return_confirmed_by',
        'return_confirmed_at',
        'rejection_reason',
        'rejected_at',
    ];

    /**
     * 🔥 CASTS — semua tanggal pakai datetime (biar ada jam)
     */
    protected $casts = [
        'borrow_date'          => 'datetime',
        'return_date'          => 'datetime',
        'expected_return_date' => 'datetime',
        'approved_at'          => 'datetime',
        'return_confirmed_at'  => 'datetime',
        'rejected_at'          => 'datetime',
    ];

    // ==================== RELATIONS ====================
    
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * 🔥 Relasi ke kode stok spesifik yang dipinjam
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

    // ==================== TENGGAT / TERLAMBAT ====================
    
    /**
     * 🔥 Cek apakah peminjaman sudah lewat tenggat.
     * Hanya berlaku untuk status 'approved' atau 'return_pending'.
     */
    public function isOverdue()
    {
        if (!in_array($this->status, ['approved', 'return_pending'])) {
            return false;
        }
        if (!$this->expected_return_date) {
            return false;
        }
        return $this->expected_return_date->isPast();
    }

    /**
     * 🔥 Hitung berapa lama terlambat (dalam format "X hari Y jam").
     * Return null kalau belum terlambat.
     */
    public function overdueDuration()
    {
        if (!$this->isOverdue()) {
            return null;
        }
        return $this->expected_return_date->diffForHumans(now(), [
            'parts'  => 2,
            'short'  => false,
            'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE,
        ]);
    }

    /**
     * 🔥 Cek apakah tenggat HARI INI.
     */
    public function isDueToday()
    {
        if (!$this->expected_return_date) {
            return false;
        }
        return $this->expected_return_date->isToday();
    }

    /**
     * 🔥 Sisa waktu sebelum tenggat (format "X jam Y menit").
     * Return null kalau sudah lewat / tenggat kosong.
     */
    public function timeUntilDue()
    {
        if (!$this->expected_return_date || $this->isOverdue()) {
            return null;
        }
        return now()->diffForHumans($this->expected_return_date, [
            'parts'  => 2,
            'short'  => true,
            'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE,
        ]);
    }

    // ==================== APPROVE ====================
    
    /**
     * 🔥 Setujui peminjaman.
     * 
     * Yang terjadi:
     * 1. Status circulation → 'approved'
     * 2. Status item_stock → 'borrowed' (kalau ada)
     */
    public function approve()
    {
        $this->status      = 'approved';
        $this->approved_at = now();
        $this->save();

        // 🔥 Update status kode stok → borrowed
        if ($this->item_stock_id && $this->itemStock) {
            $this->itemStock->markAsBorrowed(
                'Dipinjam oleh ' . $this->borrower_name . ' via circulation #' . $this->id
            );
        }

        // Update status item (opsional)
        if ($this->item && !$this->item_stock_id) {
            $this->item->status = 'borrowed';
            $this->item->save();
        }
    }

    // ==================== REJECT ====================
    
    /**
     * Tandai sirkulasi sebagai ditolak.
     */
    public function reject(?string $reason = null)
    {
        $this->status      = 'rejected';
        $this->rejected_at = now();
        if ($reason) {
            $this->rejection_reason = $reason;
        }
        $this->save();
    }

    // ==================== RETURN ====================
    
    public function requestReturn()
    {
        $this->status = 'return_pending';
        $this->save();
    }

    /**
     * 🔥 Konfirmasi pengembalian.
     * 
     * Yang terjadi:
     * 1. Status circulation → 'returned'
     * 2. Status item_stock → 'available' (kode stok kembali tersedia)
     */
    public function confirmReturn($adminId)
    {
        $this->status              = 'returned';
        $this->return_date         = now();
        $this->return_confirmed_by = $adminId;
        $this->return_confirmed_at = now();
        $this->save();

        // 🔥 Update status kode stok → available kembali
        if ($this->item_stock_id && $this->itemStock) {
            $this->itemStock->markAsAvailable(
                'Dikembalikan via circulation #' . $this->id
            );
        }

        // Update status item (opsional)
        if ($this->item && !$this->item_stock_id) {
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

    /**
     * 🔥 Scope: yang tenggat HARI INI (belum dikembalikan).
     */
    public function scopeDueToday($query)
    {
        return $query->whereIn('status', ['approved', 'return_pending'])
            ->whereDate('expected_return_date', now()->toDateString());
    }

    /**
     * 🔥 Scope: yang SUDAH LEWAT tenggat (belum dikembalikan).
     */
    public function scopeOverdue($query)
    {
        return $query->whereIn('status', ['approved', 'return_pending'])
            ->where('expected_return_date', '<', now());
    }

    /**
     * 🔥 Scope: filter by kode stok
     */
    public function scopeForStock($query, $itemStockId)
    {
        return $query->where('item_stock_id', $itemStockId);
    }
}