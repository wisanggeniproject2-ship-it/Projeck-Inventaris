<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Circulation;
use Illuminate\Http\Request;

class CirculationController extends Controller
{
    // Menampilkan daftar sirkulasi khusus untuk dipantau Manager
    public function index()
    {
        $circulations = Circulation::with(['item', 'user'])->latest()->get();
        return view('manager.circulations.index', compact('circulations'));
    }

    // Menampilkan detail sirkulasi lengkap dengan timeline log
    public function show($id)
    {
        $circulation = Circulation::with(['item', 'user', 'logs.user'])->findOrFail($id);
        return view('manager.circulations.show', compact('circulation'));
    }
}