<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UsersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row): Model|array|null
    {
        // Password sementara, otomatis di-random per user.
        $defaultPassword = 'user' . rand(1000, 9999);

        return new User([
            'name'                 => $row['name'],
            'email'                => $row['email'],
            'password'             => Hash::make($defaultPassword),
            'role'                 => 'user',
            'unit_id'              => null,
            'phone'                => null,
            'is_active'            => true,
            'must_change_password' => true,
        ]);
    }

    public function rules(): array
    {
        return [
            'name'  => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'name.required'  => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email sudah terdaftar.',
        ];
    }
}