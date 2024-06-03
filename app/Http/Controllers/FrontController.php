<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FrontController extends Controller
{
    public function index () {

        $courses = Course::with(['category', 'teacher', 'students'])->orderByDesc('id')->get(); // ambil model course bersma dengan relasinya yaitu category teacher dan students lalu orderByDesc adalah urutkan dari yang terbaru
        return view ('front.index', compact(['courses'])); // tampilkan view index ada di folder views/front/index.blade.php
    }

    // menerima parameter course karna Course memiliki slug yg berisi link details course (ini dinamakan model binding)
    public function details (Course $course) {
        return view ('front.details', compact('course')); 
    }

    public function learning(Course $course, $courseVideoId) {

        $user = Auth::user();

        // cek user sudah berlangganan atau belum

        // jika user tidak berlangganan maka redirect ke halaman front.pricing
        if(!$user->hasActiveSubscription()) {
            return redirect()->route('front.pricing');
        }

        $video = $course->course_videos->firstWhere('id', $courseVideoId); // ambil details video dari id yg sudah di klik user

        
        $user->courses()->syncWithoutDetaching($course->id); //jadikan active students ketika students sudah mulai menonton video course
    
        return view('front.learning', compact('course', 'video'));
    }
}
