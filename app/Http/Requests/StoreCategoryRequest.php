<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['owner']); // yg bisa menggunakan request ini hanyalah owner
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // reusable form request agar mudah di maintenance
        return [
            //
            // validasi data yg di kirim ke DB
            'name' => ['required', 'string', 'max:255'],
            'icon' => ['required', 'image', 'mimes:png,jpg,jpeg,svg']
        ];
    }
}
