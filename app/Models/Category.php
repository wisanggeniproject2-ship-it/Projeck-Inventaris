<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'description', 'useful_life_years'];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}