<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'category_id', 'unit_id', 'location', 
        'budget_source', 'image', 'description', 'status', 'qr_code_path'
    ];

    protected $casts = [
        'status' => 'string',
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
            ->whereIn('status', ['pending', 'approved'])
            ->latest();
    }

    public function isAvailable()
    {
        return $this->status === 'available';
    }

    public function getImageUrlAttribute()
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return Storage::url($this->image);
        }
        return asset('assets/images/default-item.png');
    }

    /**
     * Get QR Code URL (Support PNG & SVG)
     */
    public function getQrCodeUrlAttribute()
    {
        if ($this->qr_code_path && Storage::disk('public')->exists($this->qr_code_path)) {
            return Storage::url($this->qr_code_path);
        }
        
        // Fallback: cek apakah ada file QR dengan format lama (PNG)
        $pngPath = 'qrcodes/' . $this->code . '.png';
        if (Storage::disk('public')->exists($pngPath)) {
            return Storage::url($pngPath);
        }
        
        return null;
    }

    /**
     * Get QR Code file extension
     */
    public function getQrCodeExtensionAttribute()
    {
        if ($this->qr_code_path) {
            return pathinfo($this->qr_code_path, PATHINFO_EXTENSION);
        }
        return null;
    }

    /**
     * Check if QR Code exists
     */
    public function hasQrCode()
    {
        return $this->qr_code_url !== null;
    }
}