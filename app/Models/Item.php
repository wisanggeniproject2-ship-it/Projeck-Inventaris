<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Item extends Model
{
    use HasFactory;

  protected $fillable = [
    'code', 'name', 'category_id', 'unit_id', 'purchase_date',
    'condition', 'price', 'location', 'status', 'image', 'qr_code_path', 'description'
];

    protected $casts = [
        'purchase_date' => 'date',
        'price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function circulations()
    {
        return $this->hasMany(Circulation::class);
    }

    public function activeCirculation()
    {
        return $this->hasOne(Circulation::class)
            ->whereIn('status', ['approved'])
            ->latest();
    }

    public function isAvailable()
    {
        return $this->status === 'available';
    }

    public function isBroken()
    {
        return $this->condition === 'rusak' || $this->condition === 'perbaikan';
    }

    public function canBeBorrowed()
    {
        return $this->status === 'available' && $this->condition === 'baik';
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeByUnit($query, $unitId)
    {
        return $query->where('unit_id', $unitId);
    }

    // ==================== GETTER IMAGE ====================
    public function getImageUrlAttribute()
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return Storage::url($this->image);
        }
        return asset('assets/images/default-item.png');
    }

    public function getQrCodeUrlAttribute()
    {
        if ($this->qr_code_path && Storage::disk('public')->exists($this->qr_code_path)) {
            return Storage::url($this->qr_code_path);
        }
        return null;
    }

    public static function generateCode($unitId)
    {
        $unit = Unit::find($unitId);
        if (!$unit) {
            throw new \Exception('Unit tidak ditemukan');
        }

        $prefix = 'INV-' . $unit->code . '-';
        
        $lastItem = self::where('unit_id', $unitId)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastItem) {
            $lastNumber = (int) substr($lastItem->code, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }
    
}