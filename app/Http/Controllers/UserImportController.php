<?php

namespace App\Http\Controllers;

use App\Imports\UsersImport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class UserImportController extends Controller
{
    public function form(): View
    {
        return view('admin.users.import');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        $import = new UsersImport();
        Excel::import($import, $request->file('file'));

        $failures = $import->failures();

        if ($failures->count() > 0) {
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }

            return back()
                ->with('import_errors', $errors)
                ->with('warning', 'Sebagian data gagal diimport, cek detail di bawah.');
        }

        return back()->with('success', 'Semua data user berhasil diimport!');
    }
}