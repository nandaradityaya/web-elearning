<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseVideoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['teacher', 'owner']); // yg bisa menggunakan request ini hanyalah owner dan teacher
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
            'name' => 'required|string|max:255',
            'path_video' => 'required|string|max:255',
        ];
    }
}
