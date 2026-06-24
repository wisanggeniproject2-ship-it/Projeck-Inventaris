<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function scan($code)
    {
        $item = Item::with(['category', 'unit'])
            ->where('code', $code)
            ->firstOrFail();

        return view('admin.items.scan', compact('item'));
    }
}