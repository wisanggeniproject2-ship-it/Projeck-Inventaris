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
        'approved_by', 'approved_at', 'return_confirmed_by', 'return_confirmed_at',
        'rejection_reason', 'rejected_at',
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
            'parts' => 2,
            'short' => false,
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
            'parts' => 2,
            'short' => true,
            'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE,
        ]);
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

    // ==================== REJECT ====================
    /**
     * Tandai sirkulasi sebagai ditolak.
     *
     * @param  string|null  $reason  Alasan penolakan
     * @return void
     */
    public function reject(?string $reason = null)
    {
        $this->status = 'rejected';
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
}