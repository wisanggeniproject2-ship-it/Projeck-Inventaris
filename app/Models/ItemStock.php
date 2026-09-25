<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'stock_number',
        'stock_code',
        'status',
        'notes',
    ];

    protected $casts = [
        'stock_number' => 'integer',
    ];

    // ==================== RELATIONS ====================
    
    /**
     * Relasi ke Item (barang induk)
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Relasi ke pengajuan penghapusan (asset disposal)
     */
    public function disposals()
    {
        return $this->hasMany(AssetDisposal::class);
    }

    /**
     * 🔥 Relasi ke circulations (peminjaman)
     */
    public function circulations()
    {
        return $this->hasMany(Circulation::class);
    }

    /**
     * 🔥 Peminjaman yang masih aktif (belum dikembalikan)
     */
    public function activeCirculation()
    {
        return $this->hasOne(Circulation::class)
            ->whereIn('status', ['pending', 'approved', 'return_pending'])
            ->latest();
    }

    // ==================== SCOPES ====================
    
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeBorrowed($query)
    {
        return $query->where('status', 'borrowed');
    }

    public function scopeDisposed($query)
    {
        return $query->where('status', 'disposed');
    }

    public function scopeMaintenance($query)
    {
        return $query->where('status', 'maintenance');
    }

    // ==================== HELPERS ====================
    
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function isBorrowed(): bool
    {
        return $this->status === 'borrowed';
    }

    public function isDisposed(): bool
    {
        return $this->status === 'disposed';
    }

    public function isMaintenance(): bool
    {
        return $this->status === 'maintenance';
    }

    /**
     * Cek apakah kode stok ini bisa dipinjam
     */
    public function canBeBorrowed(): bool
    {
        return $this->status === 'available';
    }

    // ==================== ACTIONS ====================
    
    /**
     * 🔥 Tandai sebagai borrowed (sedang dipinjam)
     */
    public function markAsBorrowed(?string $notes = null): void
    {
        $this->update([
            'status' => 'borrowed',
            'notes'  => $notes ?? $this->notes,
        ]);
    }

    /**
     * 🔥 Tandai sebagai available (dikembalikan)
     */
    public function markAsAvailable(?string $notes = null): void
    {
        $this->update([
            'status' => 'available',
            'notes'  => $notes ?? $this->notes,
        ]);
    }

    /**
     * 🔥 Tandai sebagai disposed (rusak / dihapus)
     */
    public function markAsDisposed(?string $notes = null): void
    {
        $this->update([
            'status' => 'disposed',
            'notes'  => $notes ?? $this->notes,
        ]);
    }

    /**
     * 🔥 Tandai sebagai maintenance (perbaikan)
     */
    public function markAsMaintenance(?string $notes = null): void
    {
        $this->update([
            'status' => 'maintenance',
            'notes'  => $notes ?? $this->notes,
        ]);
    }
}