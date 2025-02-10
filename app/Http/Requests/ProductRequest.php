<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|min:5|max:50',
            'subtitle' => 'required|min:5|max:80',
            'slug' => 'required|min:5|max:80',
            'desc' => 'required|min:5',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'judul wajib diisi',
            'subtitle.required' => 'sub judul wajib diisi',
            'slug.required' => 'slug wajib diisi',
            'desc.required' => 'deskripsi wajib diisi',
            'title.min' => 'judul minimal 5 karakter',
            'subtitle.min' => 'sub judul minimal 5 karakter',
            'slug.min' => 'slug minimal 5 karakter',
            'desc.min' => 'deskripsi minimal 5 karakter',
            'title.max' => 'judul maksimal 50 karakter',
            'subtitle.max' => 'sub judul maksimal 80 karakter',
            'slug.max' => 'slug maksimal 80 karakter',
        ];
    }
}
