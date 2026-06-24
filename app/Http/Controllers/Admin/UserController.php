<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        // Middleware sudah dihandle di routes
    }

    // ==================== INDEX ====================
    public function index(Request $request)
    {
        $query = User::with('unit');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        
        $users = $query->latest()->paginate(10);
        $units = Unit::where('is_active', true)->get();
        $roles = ['super_admin', 'admin_unit', 'manager', 'user'];
        
        return view('admin.users.index', compact('users', 'units', 'roles'));
    }

    // ==================== CREATE ====================
    public function create()
    {
        $units = Unit::where('is_active', true)->get();
        $roles = ['super_admin', 'admin_unit', 'manager', 'user'];
        return view('admin.users.create', compact('units', 'roles'));
    }

    // ==================== STORE ====================
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:super_admin,admin_unit,manager,user',
            'unit_id' => 'nullable|exists:units,id',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'unit_id' => $request->unit_id,
            'phone' => $request->phone,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()->route('super_admin.users.index')
            ->with('success', 'User berhasil ditambahkan!');
    }

    // ==================== SHOW ====================
    public function show(User $user)
    {
        $user->load(['unit']);
        return view('admin.users.show', compact('user'));
    }

    // ==================== EDIT ====================
    public function edit(User $user)
    {
        $units = Unit::where('is_active', true)->get();
        $roles = ['super_admin', 'admin_unit', 'manager', 'user'];
        return view('admin.users.edit', compact('user', 'units', 'roles'));
    }

    // ==================== UPDATE ====================
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
            'role' => 'required|in:super_admin,admin_unit,manager,user',
            'unit_id' => 'nullable|exists:units,id',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $data = $request->except(['password']);
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        
        $user->update($data);

        return redirect()->route('super_admin.users.index')
            ->with('success', 'User berhasil diupdate!');
    }

    // ==================== DESTROY ====================
    public function destroy(User $user)
    {
        // Cegah menghapus diri sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri!');
        }
        
        $user->delete();
        
        return redirect()->route('super_admin.users.index')
            ->with('success', 'User berhasil dihapus!');
    }
}