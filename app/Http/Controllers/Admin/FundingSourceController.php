<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FundingSource;
use Illuminate\Http\Request;

class FundingSourceController extends Controller
{
    public function index()
    {
        $sources = FundingSource::latest()->paginate(10);
        return view('admin.funding_sources.index', compact('sources'));
    }

    public function create()
    {
        return view('admin.funding_sources.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:funding_sources',
            'code' => 'nullable|string|max:20|unique:funding_sources',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        FundingSource::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('super_admin.funding-sources.index')
            ->with('success', 'Sumber dana berhasil ditambahkan!');
    }

    public function edit(FundingSource $fundingSource)
    {
        return view('admin.funding_sources.edit', compact('fundingSource'));
    }

    public function update(Request $request, FundingSource $fundingSource)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:funding_sources,name,' . $fundingSource->id,
            'code' => 'nullable|string|max:20|unique:funding_sources,code,' . $fundingSource->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $fundingSource->update([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('super_admin.funding-sources.index')
            ->with('success', 'Sumber dana berhasil diupdate!');
    }

    public function destroy(FundingSource $fundingSource)
    {
        // Cek apakah ada barang yang menggunakan sumber dana ini
        if ($fundingSource->items()->count() > 0) {
            return back()->with('error', 'Sumber dana tidak bisa dihapus karena masih digunakan oleh barang!');
        }

        $fundingSource->delete();

        return redirect()->route('super_admin.funding-sources.index')
            ->with('success', 'Sumber dana berhasil dihapus!');
    }
}