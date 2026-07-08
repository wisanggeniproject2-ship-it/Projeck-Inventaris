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
        'condition', 'price', 'stock', 'location', 'status', 'image', 
        'qr_code_path', 'description', 'funding_source_id'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    // ==================== RELATIONS ====================
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
            ->whereIn('status', ['approved', 'pending'])
            ->latest();
    }

    public function fundingSource()
    {
        return $this->belongsTo(FundingSource::class);
    }

    // ==================== STATUS METHODS ====================
    
    // CEK APAKAH BARANG BISA DIPINJAM
    public function canBeBorrowed()
    {
        return $this->stock > 0 && $this->status === 'available' && $this->condition === 'baik';
    }

    // CEK APAKAH BARANG RUSAK ATAU PERBAIKAN
    public function isBroken()
    {
        return $this->condition === 'rusak' || $this->condition === 'perbaikan';
    }

    // CEK APAKAH BARANG TERKUNCI
    public function isLocked()
    {
        return $this->status === 'borrowed' || $this->status === 'maintenance' || $this->stock <= 0;
    }

   // ==================== STOK METHODS ====================
public function decreaseStock($qty = 1)
{
    if ($this->stock >= $qty) {
        $this->stock -= $qty;
        $this->save();
        
        // Jika stok habis, update status
        if ($this->stock <= 0) {
            $this->status = 'borrowed';
            $this->save();
        }
        return true;
    }
    return false;
}

public function increaseStock($qty = 1)
{
    $this->stock += $qty;
    $this->save();
    
    // Jika stok > 0, update status
    if ($this->stock > 0 && $this->status === 'borrowed') {
        $this->status = 'available';
        $this->save();
    }
    return true;
}
    public function isStockAvailable()
    {
        return $this->stock > 0;
    }

    // ==================== SCOPES ====================
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')
            ->where('condition', 'baik')
            ->where('stock', '>', 0);
    }

    public function scopeByUnit($query, $unitId)
    {
        return $query->where('unit_id', $unitId);
    }

    // ==================== GENERATE KODE ====================
    public static function generateCode($unitId, $categoryId, $purchaseDate = null)
    {
        $unit = Unit::find($unitId);
        if (!$unit) {
            throw new \Exception('Unit tidak ditemukan');
        }

        $category = Category::find($categoryId);
        if (!$category) {
            throw new \Exception('Kategori tidak ditemukan');
        }

        $unitCode = strtoupper(substr($unit->name, 0, 1));

        $lastItem = self::where('unit_id', $unitId)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastItem) {
            $lastNumber = (int) substr($lastItem->code, 2, 3);
            $sequence = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $sequence = '001';
        }

        $categoryCode = str_pad($category->id, 2, '0', STR_PAD_LEFT);

        $date = $purchaseDate ?? now();
        $month = $date->format('m');
        $monthRoman = self::getRomanMonth($date->month);
        $year = $date->format('Y');

        $yayasanCode = 'Y';

        return $unitCode . '/' . $sequence . '/' . $categoryCode . '/' . $month . '/' . $monthRoman . '/' . $year . '/' . $yayasanCode;
    }

    private static function getRomanMonth($month)
    {
        $romanMonths = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
            5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
            9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        return $romanMonths[$month] ?? 'I';
    }

    // ==================== GETTER ====================
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
}