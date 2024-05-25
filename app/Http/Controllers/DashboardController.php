<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseStudent;
use App\Models\SubscribeTransaction;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    //
    public function index() {
        $user = Auth::user(); // ambil siapa yg login 
        $coursesQuery = Course::query(); // query data kelas

         // jika yg login adalah guru maka querynya perdalam lagi (filter karna gaakan semua kelas di ambil)
        if($user->hasRole('teacher')) {
            // query dimana course tersebut berelasi atau dimiliki oleh teacher (query di dalam query)
            $coursesQuery->whereHas('teacher', function ($query) use ($user) {
                $query->where('user_id', $user->id); // dimana user_id pada table user = $user->id
            });

            // data ini akan di tampilkan jika yg login adalah teacher
            // ambil berapa banyak student yg ada di course milik guru tsb
            $students = CourseStudent::whereIn('course_id', $coursesQuery->select('id')) // cocokin course_id dengan id pada table courses
            ->distinct('user_id') // gunakan distinct agar lebih unique (cth: andy memiliki 3 kelas milik pak legino, maka andy tetap di hitung satu, jadi murid pak legino hanya andy 1 bukan andy 3)
            ->count('user_id'); // hitung seluruh datanya
        } else {
            // data ini akan di tampilkan jika yg login owner
            $students = CourseStudent::distinct('user_id') // gunakan distinct agar lebih unique (cth: andy memiliki 3 kelas milik pak legino, maka andy tetap di hitung satu, jadi murid pak legino hanya andy 1 bukan andy 3)
            ->count('user_id'); // hitung seluruh datanya
        }

        $courses = $coursesQuery->count();

        $categories = Category::count();
        $transactions = SubscribeTransaction::count();
        $teachers = Teacher::count();

        return view('dashboard', compact('categories', 'courses', 'transactions', 'students', 'teachers'));
    }
}
