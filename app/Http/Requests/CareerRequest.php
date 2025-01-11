<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CareerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|min:5|max:50',
            'job_open' => 'required|date|date_format:Y-m-d',
            'job_closed' => 'required|date|date_format:Y-m-d|after:job_open',
            'desc' => 'required|min:5',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'judul wajib diisi',
            'desc.required' => 'deskripsi wajib diisi',
            'title.min' => 'judul minimal 5 karakter',
            'title.max' => 'judul maksimal 50 karakter',
            'desc.min' => 'deskripsi minimal 5 karakter',
            'job_open.required' => 'tanggal mulai wajib diisi',
            'job_closed.required' => 'tanggal selesai wajib diisi',
            'job_closed.after' => 'tanggal selesai harus lebih dari tanggal mulai',
            'job_open.date' => 'tanggal mulai harus berupa tanggal',
            'job_closed.date' => 'tanggal selesai harus berupa tanggal',
            'job_open.date_format' => 'format tanggal mulai harus yyyy-mm-dd',
            'job_closed.date_format' => 'format tanggal selesai harus yyyy-mm-dd',
        ];
    }
}
