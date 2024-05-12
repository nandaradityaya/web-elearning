<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
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
            'path_trailer' => 'required|string|max:255',
            'about' => 'required|string',
            'category_id' => 'required|integer',
            'thumbnail' => 'sometimes|image|mimes:png,jpg,jpeg,svg', // gunakan sometimes karna terkadang user masih ingin menggunakan thumbnail yg lama, yg di edit hanyalah name dan lain2
            'course_keypoints.*' => 'required|string|max:255',
        ];
    }
}
