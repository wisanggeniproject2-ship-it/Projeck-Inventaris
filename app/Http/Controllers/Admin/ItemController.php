<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Category;
use App\Models\Unit;
use App\Http\Requests\ItemRequest;
use App\Services\QRCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    protected $qrCodeService;

    public function __construct(QRCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    public function index(Request $request)
    {
        $query = Item::with(['category', 'unit']);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }
        
        $items = $query->latest()->paginate(10);
        
        return view('admin.items.index', compact('items'));
    }

    public function create()
    {
        $categories = Category::all();
        $units = Unit::all();
        return view('admin.items.create', compact('categories', 'units'));
    }

    public function store(ItemRequest $request)
    {
        $data = $request->validated();
        
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('items', 'public');
        }
        
        $item = Item::create($data);
        
        // Generate QR Code
        $qrCodePath = $this->qrCodeService->generateQrCode($item);
        $item->update(['qr_code_path' => $qrCodePath]);
        
        return redirect()->route('admin.items.index')
            ->with('success', 'Item created successfully.');
    }

    public function show(Item $item)
    {
        $item->load(['category', 'unit', 'circulations.user']);
        return view('admin.items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        $categories = Category::all();
        $units = Unit::all();
        return view('admin.items.edit', compact('item', 'categories', 'units'));
    }

    public function update(ItemRequest $request, Item $item)
    {
        $data = $request->validated();
        
        if ($request->hasFile('image')) {
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $data['image'] = $request->file('image')->store('items', 'public');
        }
        
        $item->update($data);
        
        // Regenerate QR Code if needed
        if ($item->wasChanged('code')) {
            $qrCodePath = $this->qrCodeService->generateQrCode($item);
            $item->update(['qr_code_path' => $qrCodePath]);
        }
        
        return redirect()->route('admin.items.index')
            ->with('success', 'Item updated successfully.');
    }

    public function destroy(Item $item)
    {
        if ($item->image) {
            Storage::disk('public')->delete($item->image);
        }
        if ($item->qr_code_path) {
            Storage::disk('public')->delete($item->qr_code_path);
        }
        
        $item->delete();
        
        return redirect()->route('admin.items.index')
            ->with('success', 'Item deleted successfully.');
    }
}