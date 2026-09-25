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
        'code', 
        'full_code',           // 🔥 Kode lengkap (format baru)
        'name', 
        'category_id', 
        'unit_id', 
        'purchase_date',
        'condition', 
        'price', 
        'stock', 
        'disposed_stock',      // 🔥 Total yang sudah di-dispose
        'location', 
        'status', 
        'image', 
        'qr_code_path', 
        'description', 
        'funding_source_id'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'price' => 'decimal:2',
        'stock' => 'integer',
        'disposed_stock' => 'integer',
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

    // ============================================================
    // 🔥 RELASI KE ITEM_STOCKS (KODE PER UNIT STOK)
    // ============================================================
    
    /**
     * Semua kode stok (termasuk yang disposed/borrowed)
     */
    public function stockCodes()
    {
        return $this->hasMany(ItemStock::class)->orderBy('stock_number');
    }

    /**
     * Kode stok yang masih available (belum rusak/dipinjam)
     */
    public function availableStockCodes()
    {
        return $this->hasMany(ItemStock::class)
            ->where('status', 'available')
            ->orderBy('stock_number');
    }

    /**
     * Kode stok yang sudah disposed
     */
    public function disposedStockCodes()
    {
        return $this->hasMany(ItemStock::class)
            ->where('status', 'disposed')
            ->orderBy('stock_number');
    }

    // ============================================================
    // 🔥 ACCESSOR — STOK DETAIL
    // ============================================================

    /**
     * 🔥 Stok tersedia = hitung LIVE dari item_stocks yang available
     */
    public function getAvailableStockAttribute(): int
    {
        return $this->stockCodes()->where('status', 'available')->count();
    }

    /**
     * 🔥 Stok sedang dipinjam (borrowed)
     */
    public function getBorrowedStockAttribute(): int
    {
        return $this->stockCodes()->where('status', 'borrowed')->count();
    }

    /**
     * 🔥 Stok disposed (rusak/hilang)
     */
    public function getDisposedStockCountAttribute(): int
    {
        return $this->stockCodes()->where('status', 'disposed')->count();
    }

    /**
     * 🔥 Stok maintenance
     */
    public function getMaintenanceStockAttribute(): int
    {
        return $this->stockCodes()->where('status', 'maintenance')->count();
    }

    /**
     * 🔥 Total fisik AKTIF (exclude disposed) — untuk display
     */
    public function getTotalActiveStockAttribute(): int
    {
        return $this->stockCodes()->where('status', '!=', 'disposed')->count();
    }

    /**
     * 🔥 Total fisik SEMUA (termasuk disposed) — untuk audit
     */
    public function getTotalPhysicalStockAttribute(): int
    {
        return $this->stockCodes()->count();
    }

    /**
     * 🔥🔥🔥 STOCK FOR ASSET — KHUSUS UNTUK NILAI ASET
     * 
     * Rumus: Ready + Dipinjam + Maintenance
     * (Disposed dianggap HILANG MUSNAH, tidak dihitung)
     */
    public function getStockForAssetAttribute(): int
    {
        return $this->stockCodes()
            ->whereIn('status', ['available', 'borrowed', 'maintenance'])
            ->count();
    }

    // ============================================================
    // 🔥 SYNC STOK — AUTO UPDATE items.stock
    // ============================================================

    /**
     * 🔥 Sync items.stock dari hitungan item_stocks yang available
     * 
     * Dipanggil otomatis oleh ItemStockObserver
     */
    public function syncStockFromItemStocks(): void
    {
        // Kalau belum punya stock codes sama sekali, JANGAN diubah
        if ($this->stockCodes()->count() === 0) {
            return;
        }

        $availableCount = $this->stockCodes()->where('status', 'available')->count();

        // Pakai updateQuietly biar tidak trigger event loop
        $this->updateQuietly(['stock' => $availableCount]);

        // Refresh model biar nilai terbaru kebaca
        $this->refresh();
    }

    /**
     * 🔥 Cek apakah item sudah punya stock codes
     */
    public function hasStockCodes(): bool
    {
        return $this->stockCodes()->exists();
    }

    // ==================== STATUS METHODS ====================
    
    public function canBeBorrowed()
    {
        return $this->stock > 0 && $this->status === 'available' && $this->condition === 'baik';
    }

    public function isBroken()
    {
        return $this->condition === 'rusak' || $this->condition === 'perbaikan';
    }

    public function isLocked()
    {
        return $this->status === 'borrowed' || $this->status === 'maintenance' || $this->stock <= 0;
    }

    public function isDisposed()
    {
        return $this->status === 'disposed';
    }

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
        
        if ($this->stock > 0 && $this->status === 'borrowed') {
            $this->status = 'available';
            $this->save();
        }
        return true;
    }

    /**
     * 🔥 Kurangi stok karena penghapusan aset
     */
    public function disposeStock($qty = 1)
    {
        // Safety net: nggak boleh mengurangi lebih dari stok yang ada
        $qty = min($qty, $this->stock);

        $this->stock -= $qty;
        $this->disposed_stock = ($this->disposed_stock ?? 0) + $qty;

        // Barang baru berstatus disposed/rusak kalau SEMUA stoknya habis
        if ($this->stock <= 0) {
            $this->stock = 0;
            $this->status = 'disposed';
            $this->condition = 'rusak';
        }

        $this->save();
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

    // ==================== GENERATE KODE LAMA ====================
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

        // 🔥 PREFIX UNIK PER UNIT
        if ($unit->id <= 26) {
            $unitCode = chr(64 + (int) $unit->id);
        } else {
            $first  = chr(64 + (int) floor(($unit->id - 1) / 26));
            $second = chr(65 + (($unit->id - 1) % 26));
            $unitCode = $first . $second;
        }

        // 🔥 AMBIL NOMOR URUT TERBESAR
        $lastItem = self::where('code', 'LIKE', $unitCode . '/%')
            ->orderByRaw('CAST(SUBSTRING(code, 3, 3) AS UNSIGNED) DESC')
            ->first();

        if ($lastItem) {
            $lastNumber = (int) substr($lastItem->code, 2, 3);
            $sequence   = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $sequence = '001';
        }

        $categoryCode = str_pad($category->id, 2, '0', STR_PAD_LEFT);

        $date = $purchaseDate ?? now();
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        $month       = $date->format('m');
        $monthRoman  = self::getRomanMonth($date->month);
        $year        = $date->format('Y');
        $yayasanCode = 'Y';

        $code = $unitCode . '/' . $sequence . '/' . $categoryCode . '/' . $month . '/' . $monthRoman . '/' . $year . '/' . $yayasanCode;

        // 🔥 SAFETY NET
        $attempt = 1;
        while (self::where('code', $code)->exists()) {
            $sequence = str_pad((int) $sequence + 1, 3, '0', STR_PAD_LEFT);
            $code = $unitCode . '/' . $sequence . '/' . $categoryCode . '/' . $month . '/' . $monthRoman . '/' . $year . '/' . $yayasanCode;

            $attempt++;
            if ($attempt > 100) break;
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

    // ============================================================
    // 🔥 GENERATE KODE BARU — FULL CODE (format baru)
    // ============================================================

    /**
     * 🔥 Generate & simpan full_code
     */
    public function generateFullCode(int $stockNumber = 1): string
    {
        $generator = app(\App\Services\ItemCodeGenerator::class);
        $this->full_code = $generator->generate($this, $stockNumber);
        $this->save();
        return $this->full_code;
    }

    /**
     * 🔥 Get full_code per stok (generate dari full_code utama)
     */
    public function getStockCode(int $stockNumber): ?string
    {
        if (!$this->full_code) {
            return null;
        }

        return preg_replace(
            '/\.\d{2}\//',
            '.' . str_pad($stockNumber, 2, '0', STR_PAD_LEFT) . '/',
            $this->full_code,
            1
        );
    }

    /**
     * 🔥 Get semua kode stok dari tabel item_stocks (hanya available)
     */
    public function getAllStockCodes(): array
    {
        return $this->availableStockCodes()
            ->get()
            ->map(fn($s) => [
                'id'   => $s->id,
                'no'   => $s->stock_number,
                'code' => $s->stock_code,
            ])
            ->toArray();
    }

    /**
     * 🔥 Get semua kode stok (termasuk yang disposed)
     */
    public function getAllStockCodesWithDisposed(): array
    {
        return $this->stockCodes()
            ->get()
            ->map(fn($s) => [
                'id'     => $s->id,
                'no'     => $s->stock_number,
                'code'   => $s->stock_code,
                'status' => $s->status,
            ])
            ->toArray();
    }

    // ============================================================
    // 🔥🔥🔥 SYNC STOCK CODES — RESET NOMOR (FIX NUMPUK)
    // ============================================================

    /**
     * 🔥 Sync kode stok ke tabel item_stocks — RESET NOMOR
     * 
     * ATURAN:
     * - Kode dengan status borrowed/disposed/maintenance → DIPERTAHANKAN
     * - Kode dengan status available → DIHAPUS, lalu di-generate ulang
     * - Nomor selalu di-reset dari .01 (skip nomor yang sudah dipakai)
     * 
     * Contoh:
     *   Awal: .01 s/d .40 (semua available)
     *   Edit jadi 4 → hapus semua, generate .01-.04 (BERSIH!)
     *   
     *   Awal: .01-.05 available, .06 borrowed
     *   Edit jadi 5 → hapus .01-.05, generate .01-.04 (skip .06)
     *                 Hasil: .01, .02, .03, .04, .06 (borrowed)
     */
    public function syncStockCodes(): void
    {
        // Pastikan full_code ada
        if (!$this->full_code) {
            $this->generateFullCode();
            $this->refresh();
        }

        $targetStock = (int) $this->stock;

        // ============================================================
        // 🔥 STEP 1: Ambil nomor yang DIPERTAHANKAN (tidak boleh dihapus)
        // ============================================================
        $protectedStocks = $this->stockCodes()
            ->whereIn('status', ['borrowed', 'disposed', 'maintenance'])
            ->get();

        $protectedNumbers = $protectedStocks->pluck('stock_number')->toArray();
        $protectedCount   = count($protectedNumbers);

        // ============================================================
        // 🔥 STEP 2: Hapus semua kode yang statusnya AVAILABLE
        // ============================================================
        $this->stockCodes()
            ->where('status', 'available')
            ->delete();

        // ============================================================
        // 🔥 STEP 3: Hitung berapa kode baru yang perlu di-generate
        // ============================================================
        $needToGenerate = $targetStock - $protectedCount;

        if ($needToGenerate <= 0) {
            if ($needToGenerate < 0) {
                \Log::warning(
                    "Item #{$this->id} ({$this->name}): Target stok ({$targetStock}) " .
                    "lebih kecil dari kode yang dilindungi ({$protectedCount}). " .
                    "Kode tidak bisa dikurangi."
                );
            }
            return;
        }

        // ============================================================
        // 🔥 STEP 4: Generate kode baru, mulai dari .01, skip nomor terpakai
        // ============================================================
        $counter   = 1;
        $generated = 0;

        while ($generated < $needToGenerate) {
            // Skip nomor yang sudah dipakai (borrowed/disposed/maintenance)
            if (in_array($counter, $protectedNumbers)) {
                $counter++;
                continue;
            }

            $code = $this->getStockCode($counter);
            if (!$code) {
                $counter++;
                continue;
            }

            ItemStock::create([
                'item_id'      => $this->id,
                'stock_number' => $counter,
                'stock_code'   => $code,
                'status'       => 'available',
            ]);

            $generated++;
            $counter++;

            // Safety net biar tidak infinite loop
            if ($counter > 9999) {
                \Log::error("Item #{$this->id}: Infinite loop di syncStockCodes()");
                break;
            }
        }
    }

    /**
     * 🔥 Accessor: full_code_formatted
     */
    public function getFullCodeFormattedAttribute(): string
    {
        return $this->full_code ?? $this->code ?? '-';
    }

    // ==================== PENYUSUTAN ASET ====================

    public function getUsefulLifeYears()
    {
        return $this->category->useful_life_years ?? 5;
    }

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

    public function getYearsInUse()
    {
        if (!$this->purchase_date) {
            return 0;
        }

        $years = $this->purchase_date->diffInDays(now()) / 365;
        return max(0, $years);
    }

    public function getAccumulatedDepreciation()
    {
        if (!$this->price || !$this->purchase_date) {
            return 0;
        }

        $usefulLife = $this->getUsefulLifeYears();
        $yearsInUse = min($this->getYearsInUse(), $usefulLife);
        $accumulated = $this->getAnnualDepreciation() * $yearsInUse;

        return round(min($accumulated, $this->price), 2);
    }

    public function getBookValue()
    {
        if (!$this->price) {
            return 0;
        }

        $bookValue = $this->price - $this->getAccumulatedDepreciation();
        return round(max($bookValue, 0), 2);
    }

    public function getDepreciationPercentage()
    {
        if (!$this->price || $this->price <= 0) {
            return 0;
        }

        return round(($this->getAccumulatedDepreciation() / $this->price) * 100, 1);
    }

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