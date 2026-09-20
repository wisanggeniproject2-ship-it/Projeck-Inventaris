<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

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

    public function disposals()
    {
        return $this->hasMany(AssetDisposal::class);
    }

    public function activeDisposalRequest()
    {
        return $this->hasOne(AssetDisposal::class)->where('status', 'pending')->latest();
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

    // CEK APAKAH BARANG SUDAH DIHAPUS/DINONAKTIFKAN
    public function isDisposed()
    {
        return $this->status === 'disposed';
    }

    // CEK APAKAH BARANG SEDANG ADA PENGAJUAN PENGHAPUSAN YANG MASIH PENDING
    public function hasPendingDisposalRequest()
    {
        return $this->disposals()->where('status', 'pending')->exists();
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
    /**
     * Generate kode unik untuk item.
     * Format: {prefixUnit}/{sequence}/{categoryId}/{month}/{romanMonth}/{year}/Y
     * Contoh: A/001/01/09/IX/2026/Y
     *
     * Prefix unit pakai HURUF UNIK berdasarkan unit_id:
     * - unit_id 1 → A
     * - unit_id 2 → B
     * - unit_id 4 → D
     * - unit_id 5 → E
     * dst.
     * Ini mencegah bentrok kalau ada 2 unit dengan huruf pertama nama yang sama.
     */
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

        // 🔥 PREFIX UNIK PER UNIT — pakai unit_id, bukan huruf pertama nama unit
        // Rumus: chr(64 + $unit->id) → 64 = 'A', jadi unit_id 1 = 'A', 2 = 'B', dst.
        // Kalau unit_id > 26, fallback ke 2 huruf (AA, AB, dst) — pakai mod 26
        if ($unit->id <= 26) {
            $unitCode = chr(64 + (int) $unit->id);
        } else {
            // Fallback untuk unit_id > 26: pakai kombinasi 2 huruf
            $first  = chr(64 + (int) floor(($unit->id - 1) / 26));
            $second = chr(65 + (($unit->id - 1) % 26));
            $unitCode = $first . $second;
        }

        // 🔥 AMBIL NOMOR URUT TERBESAR dari kode dengan prefix yang sama
        $lastItem = self::where('code', 'LIKE', $unitCode . '/%')
            ->orderByRaw('CAST(SUBSTRING(code, 3, 3) AS UNSIGNED) DESC')
            ->first();

        if ($lastItem) {
            // Ambil 3 karakter setelah prefix (misal "A/001/..." → ambil "001")
            $lastNumber = (int) substr($lastItem->code, 2, 3);
            $sequence   = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $sequence = '001';
        }

        $categoryCode = str_pad($category->id, 2, '0', STR_PAD_LEFT);

        // Parse tanggal kalau string
        $date = $purchaseDate ?? now();
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        $month       = $date->format('m');
        $monthRoman  = self::getRomanMonth($date->month);
        $year        = $date->format('Y');
        $yayasanCode = 'Y';

        $code = $unitCode . '/' . $sequence . '/' . $categoryCode . '/' . $month . '/' . $monthRoman . '/' . $year . '/' . $yayasanCode;

        // 🔥 SAFETY NET — kalau masih duplikat, increment sampai unik
        $attempt = 1;
        while (self::where('code', $code)->exists()) {
            $sequence = str_pad((int) $sequence + 1, 3, '0', STR_PAD_LEFT);
            $code = $unitCode . '/' . $sequence . '/' . $categoryCode . '/' . $month . '/' . $monthRoman . '/' . $year . '/' . $yayasanCode;

            $attempt++;
            if ($attempt > 100) break; // biar tidak infinite loop
        }

        return $code;
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

    // ==================== PENYUSUTAN ASET ====================

    /**
     * Masa manfaat (tahun) diambil dari kategori barang.
     */
    public function getUsefulLifeYears()
    {
        return $this->category->useful_life_years ?? 5;
    }

    /**
     * Penyusutan per tahun (metode garis lurus, nilai sisa = 0).
     * Rumus: Harga Beli / Masa Manfaat
     */
    public function getAnnualDepreciation()
    {
        if (!$this->price || !$this->purchase_date) {
            return 0;
        }

        $usefulLife = $this->getUsefulLifeYears();
        if ($usefulLife <= 0) {
            return 0;
        }

        return round($this->price / $usefulLife, 2);
    }

    /**
     * Jumlah tahun sejak tanggal beli (bisa pecahan, dihitung proporsional).
     */
    public function getYearsInUse()
    {
        if (!$this->purchase_date) {
            return 0;
        }

        $years = $this->purchase_date->diffInDays(now()) / 365;
        return max(0, $years);
    }

    /**
     * Total penyusutan yang sudah berjalan (akumulasi).
     * Tidak akan melebihi harga beli.
     */
    public function getAccumulatedDepreciation()
    {
        if (!$this->price || !$this->purchase_date) {
            return 0;
        }

        $usefulLife = $this->getUsefulLifeYears();
        $yearsInUse = min($this->getYearsInUse(), $usefulLife); // gak lewat masa manfaat
        $accumulated = $this->getAnnualDepreciation() * $yearsInUse;

        return round(min($accumulated, $this->price), 2);
    }

    /**
     * Nilai buku saat ini (Harga Beli - Akumulasi Penyusutan).
     * Minimal Rp 0.
     */
    public function getBookValue()
    {
        if (!$this->price) {
            return 0;
        }

        $bookValue = $this->price - $this->getAccumulatedDepreciation();
        return round(max($bookValue, 0), 2);
    }

    /**
     * Persentase penyusutan yang sudah terjadi (0-100%).
     */
    public function getDepreciationPercentage()
    {
        if (!$this->price || $this->price <= 0) {
            return 0;
        }

        return round(($this->getAccumulatedDepreciation() / $this->price) * 100, 1);
    }

    /**
     * Cek apakah barang sudah habis masa manfaatnya.
     */
    public function isFullyDepreciated()
    {
        return $this->getYearsInUse() >= $this->getUsefulLifeYears();
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