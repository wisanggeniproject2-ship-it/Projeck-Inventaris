<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Circulation extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id', 'user_id', 'borrower_name', 'borrow_date', 'return_date',
        'expected_return_date', 'status', 'purpose', 'notes', 'approved_by', 'approved_at'
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'return_date' => 'date',
        'expected_return_date' => 'date',
        'approved_at' => 'datetime',
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

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isReturned()
    {
        return $this->status === 'returned';
    }

   public function approve()
{
    $this->status = 'approved';
    $this->approved_at = now();
    $this->save();
    
    // Update status item menjadi borrowed
    if ($this->item) {
        $this->item->status = 'borrowed';
        $this->item->save();
    }
}

public function reject()
{
    $this->status = 'rejected';
    $this->save();
    
    // Status item tetap available (tidak berubah)
}

public function markAsReturned()
{
    $this->status = 'returned';
    $this->return_date = now();
    $this->save();
    
    // Update status item menjadi available kembali
    if ($this->item) {
        $this->item->status = 'available';
        $this->item->save();
    }
}
}