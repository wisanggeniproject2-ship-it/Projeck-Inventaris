<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Circulation;
use App\Models\Item;
use Illuminate\Http\Request;

class CirculationController extends Controller
{
    public function index(Request $request)
    {
        $query = Circulation::with(['item', 'user', 'approver']);
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $circulations = $query->latest()->paginate(10);
        
        return view('admin.circulations.index', compact('circulations'));
    }

    public function approve(Circulation $circulation)
    {
        if (!$circulation->isPending()) {
            return back()->with('error', 'This circulation cannot be approved.');
        }
        
        $circulation->approve();
        $circulation->approved_by = auth()->id();
        $circulation->save();
        
        return back()->with('success', 'Circulation approved successfully.');
    }

    public function reject(Circulation $circulation)
    {
        if (!$circulation->isPending()) {
            return back()->with('error', 'This circulation cannot be rejected.');
        }
        
        $circulation->reject();
        $circulation->save();
        
        return back()->with('success', 'Circulation rejected successfully.');
    }

    public function markReturned(Circulation $circulation)
    {
        if (!$circulation->isApproved()) {
            return back()->with('error', 'Only approved circulations can be marked as returned.');
        }
        
        $circulation->markAsReturned();
        $circulation->save();
        
        return back()->with('success', 'Item marked as returned successfully.');
    }

    public function show(Circulation $circulation)
    {
        $circulation->load(['item', 'user', 'approver']);
        return view('admin.circulations.show', compact('circulation'));
    }
}