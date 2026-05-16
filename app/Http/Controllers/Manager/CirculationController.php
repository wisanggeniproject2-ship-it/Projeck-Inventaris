<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Circulation;
use Illuminate\Http\Request;

class CirculationController extends Controller
{
    public function index(Request $request)
    {
        $unitId = auth()->user()->unit_id;
        
        $query = Circulation::with(['item', 'user'])
            ->whereHas('item', function($q) use ($unitId) {
                $q->where('unit_id', $unitId);
            });
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $circulations = $query->latest()->paginate(10);
        
        return view('manager.circulations.index', compact('circulations'));
    }

    public function show(Circulation $circulation)
    {
        // Pastikan circulation milik unit manager
        if ($circulation->item->unit_id !== auth()->user()->unit_id) {
            abort(403);
        }
        
        $circulation->load(['item', 'user', 'approver']);
        
        return view('manager.circulations.show', compact('circulation'));
    }
}