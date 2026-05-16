<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $category = $this->route('category');
        $id = $category ? $category->id : null;
        
        return [
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:categories,code,' . $id,
            'description' => 'nullable|string',
        ];
    }
}