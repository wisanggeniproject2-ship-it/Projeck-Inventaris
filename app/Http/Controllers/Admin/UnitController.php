<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function __construct()
    {
        // HAPUS: $this->middleware('role:super_admin');
    }

    public function index(Request $request)
    {
        $query = Unit::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }
        
        $units = $query->latest()->paginate(10);
        return view('admin.units.index', compact('units'));
    }

    public function create()
    {
        return view('admin.units.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:units',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        Unit::create($request->all());
        return redirect()->route('super_admin.units.index')
            ->with('success', 'Unit berhasil ditambahkan');
    }

    public function edit(Unit $unit)
    {
        return view('admin.units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:units,code,' . $unit->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        $unit->update($request->all());
        return redirect()->route('super_admin.units.index')
            ->with('success', 'Unit berhasil diupdate');
    }

    public function destroy(Unit $unit)
    {
        if ($unit->users()->count() > 0 || $unit->items()->count() > 0) {
            return back()->with('error', 'Unit tidak bisa dihapus karena masih memiliki data terkait');
        }
        
        $unit->delete();
        return redirect()->route('super_admin.units.index')
            ->with('success', 'Unit berhasil dihapus');
    }
}