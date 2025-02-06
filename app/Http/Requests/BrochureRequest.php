<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BrochureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|min:5|max:50',
            'is_choosen' => 'required|boolean|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'judul wajib diisi',
            'is_choosen.required' => 'status tampil wajib diisi',
            'title.min' => 'judul minimal 5 karakter',
            'title.max' => 'judul maksimal 50 karakter',
            'is_choosen.boolean' => 'status tampil hanya berupa true atau false',
            'is_choosen.in' => 'invalid status tampil',
        ];
    }
}
