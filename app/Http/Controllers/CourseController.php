<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Category;
use App\Models\Course;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // get seluruh data kelas dan menampilkannya
        // dapat diakses oleh teacher dan owner
        // jika teacher yg akses maka hanya menampilkan data kelas yg di miliki oleh teacher tsb

        $user = Auth::user(); // ambil siapa yg login
        $query = Course::with(['category', 'teacher', 'students'])->orderByDesc('id'); // get semua course bersama dengan category, teacher, dan jumlah student lalu orderbydesc yaitu urutkan dari yang paling terbaru

        // jika yg login adalah guru maka querynya perdalam lagi (filter karna gaakan semua kelas di ambil)
        if($user->hasRole('teacher')) {
            // query dimana course tersebut berelasi atau dimiliki oleh teacher (query di dalam query)
            $query->whereHas('teacher', function ($query) use ($user) {
                $query->where('user_id', $user->id); // dimana user_id pada table user = $user->id
            });
        }

        $courses = $query->paginate(10); // pagination sebanyak 10

        return view('admin.courses.index', compact('courses')); // compact coursesnya karna mau di tampilin pada bagian index
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $categories = Category::all(); // ambil semua category butuh ini karna course memiliki category
        return view('admin.courses.create', compact('categories')); // lempar categories juga
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseRequest $request)
    {
        //
        $teacher = Teacher::where('user_id', Auth::user()->id)->first(); // cek data teacher

        // jika bukan guru maka lempar kembali ke halaman course index
        if(!$teacher) {
            return redirect()->route('admin.courses.index')->withErrors('Unauthorized or invalid teacher');
        }

        DB::transaction(function () use ($request, $teacher){

            // validasinya ada di form request tersendiri di StoreCourseRequest.php
            $validated = $request->validated();

            // samakan dengan input namenya yaitu "thumbnail"
            if($request->hasFile('thumbnail')) {
                // ambil pathnya dan simpan dalam folder thumbnails dan simpan secara public
                $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public'); 
                $validated['thumbnail'] = $thumbnailPath; // gunakan ini agar tidak private (urlnya harus dari public)
            } else {
                $iconPath = 'images/icon-default.png'; // default image jika tdk ada image dr user
            }

            $validated['slug'] = Str::slug($validated['name']); // gunakan slug agar urlnya dari web design menjadi web-design (tergenerate sendiri)
            
            $validated['teacher_id'] = $teacher->id; // ambil teacher_id dari Auth $teacher diatas

            $course = Course::create($validated); // create data terbaru dengan name, icon, dan slug
            
            // cek keypoints
            if(!empty($validated['course_keypoints'])) {
                // lakukan perulangan insert sebanyak 4x karna keypoint course ada 4
                foreach($validated['course_keypoints'] as $keypointText) {
                    // langsung ambil dan create course_keypoints karna dia berelasi
                    $course->course_keypoints()->create([
                        'name' => $keypointText
                    ]);
                }
            }
        });

        return redirect()->route('admin.courses.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        //
        return view('admin.courses.show', compact('course')); // lempar course juga
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        //
        $categories = Category::all(); // ambil semua category
        return view('admin.courses.edit', compact('course', 'categories')); // lempar course juga
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseRequest $request, Course $course)
    {
        //
        // ambil $request dan ambil $course untuk mengambil data kelas yg akan di update
        DB::transaction(function () use ($request, $course){

            // validasinya ada di form request tersendiri di UpdateCourseRequest.php
            $validated = $request->validated();

            // samakan dengan input namenya yaitu "thumbnail"
            if($request->hasFile('thumbnail')) {
                // ambil pathnya dan simpan dalam folder thumbnails dan simpan secara public
                $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public'); 
                $validated['thumbnail'] = $thumbnailPath; // gunakan ini agar tidak private (urlnya harus dari public)
            } else {
                $iconPath = 'images/icon-default.png'; // default image jika tdk ada image dr user
            }

            $validated['slug'] = Str::slug($validated['name']); // gunakan slug agar urlnya dari web design menjadi web-design (tergenerate sendiri)

            $course->update($validated);
            
            // cek keypoints
            if(!empty($validated['course_keypoints'])) {
                $course->course_keypoints()->delete(); // delete dulu keypointsnya baru deh update dgn yg baru
                
                // lakukan perulangan insert sebanyak 4x karna keypoint course ada 4
                foreach($validated['course_keypoints'] as $keypointText) {
                    // langsung ambil dan create course_keypoints karna dia berelasi
                    $course->course_keypoints()->create([
                        'name' => $keypointText
                    ]);
                }
            }
        });

        return redirect()->route('admin.courses.show', $course); // redirect ke halaman show dan kirimkan coursenya (parameter kedua itu $course utk mengirim course di halaman client)
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        //
        DB::beginTransaction();

        try {
            $course->delete(); // ambil course mana yg di delete
            DB::commit(); // commit deletenya

            return redirect()->route('admin.courses.index');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.courses.index')->with('error', 'something error'); // balikin ke index errornya dan munculkan pesan something error
        }
    }
}
