<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeacherRequest;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $teachers = Teacher::orderBy('id', 'desc')->get(); // ambil semua model Teacher
        // dd($teachers);

        return view('admin.teachers.index', [
            'teachers' => $teachers // lembar variable teacher utk menampilkan data guru
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.teachers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTeacherRequest $request)
    {
        //
        // validasinya ada di form request tersendiri di StoreTeacherRequest.php
        $validated = $request->validated();

        // ambil data user yg akan dijadikan teacher (ambil berdasarkan email lalu validasi emailnya dan ambil yg pertama)
        $user = User::where('email', $validated['email'])->first(); 

        // jika email tidak ditemukan user jalankan ini
        if(!$user) {
            return back()->withErrors([
                'email' => 'Data tidak ditemukan'
            ]);
        }

        // jika user tsb sudah menjadi role guru
        if($user->hasRole('teacher')) {
            return back()->withErrors([
                'email' => 'Email tersebut telah menjadi guru'
            ]);
        }

        DB::transaction(function () use ($user, $validated) {
            $validated['user_id'] = $user->id; // user_id isi dengan data user kita ambil idnya, IDnya di dapat dari email yg di masukan kedalam form oleh user
            $validated['is_active'] = true;

            Teacher::create($validated);

            // hapus dulu role awalnya yaitu student
            if ($user->hasRole('student')) {
                $user->removeRole('student');
            }

            // lalu assign jadi teacher
            $user->assignRole('teacher');
        });

        return redirect()->route('admin.teachers.index');

    }

    /**
     * Display the specified resource.
     */
    public function show(Teacher $teacher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Teacher $teacher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Teacher $teacher)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teacher $teacher)
    {
        //
        try {
            // semua ini hanya menghapus teacher dari table teacher, dan tidak menghapusnya dari tabel user. jadi ketika teacher di hapus maka hanya role sebagai teacher yg terhapus sehingga dia balik lagi menjadi role awal yaitu student dan dia tetap berada pada table user
            $teacher->delete();

            $user = \App\Models\User::find($teacher->user_id); // cari dulu
            $user->removeRole('teacher'); // remove role teachernya
            $user->assignRole('student'); // assign kembali rolenya menjadi student

            return redirect()->back(); 


        } catch (\Exception $e) {
            DB::rollBack();
            $error = ValidationException::withMessages([
                'system_error' => ['System error!' . $e->getMessage()],
            ]);

            throw $error;
        }
    }
}
