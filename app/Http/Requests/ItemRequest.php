<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Pastikan user login dan memiliki role admin
        if (!Auth::check()) {
            return false;
        }
        
        return Auth::user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $item = $this->route('item');
        $id = $item ? $item->id : null;
        
        return [
            'code' => 'required|string|max:50|unique:items,code,' . $id,
            'name' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'location' => 'required|string|max:200',
            'budget_source' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:available,borrowed,maintenance',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'code.required' => 'Kode barang wajib diisi',
            'code.unique' => 'Kode barang sudah digunakan',
            'name.required' => 'Nama barang wajib diisi',
            'category_id.required' => 'Kategori wajib dipilih',
            'category_id.exists' => 'Kategori tidak valid',
            'unit_id.required' => 'Unit wajib dipilih',
            'unit_id.exists' => 'Unit tidak valid',
            'location.required' => 'Lokasi wajib diisi',
            'image.image' => 'File harus berupa gambar',
            'image.mimes' => 'Format gambar harus JPG, PNG, atau JPEG',
            'image.max' => 'Ukuran gambar maksimal 2MB',
            'status.in' => 'Status tidak valid',
        ];
    }
}