<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubscribeTransactionRequest;
use App\Models\Category;
use App\Models\Course;
use App\Models\SubscribeTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function category(Category $category)
    {
      $coursesByCategory = $category->courses()->get();
  
      return view('front.category', compact('coursesByCategory', 'category'));
    }

    public function pricing () {
         // jika user sudah memiliki paket berjalan maka tidak boleh ke halaman pricing
        if (Auth::user()->hasActiveSubscription()) {
            return redirect()->route('front.index');
        }
  
        return view ('front.pricing'); 
    }

    public function checkout () {

         // jika user sudah memiliki paket berjalan maka tidak boleh ke halaman checkout
        if (Auth::user()->hasActiveSubscription()) {
            return redirect()->route('front.index');
        }

        return view ('front.checkout'); 
    }

    public function checkout_store(StoreSubscribeTransactionRequest $request)
    {
      $user = Auth::user();
  
      // jika user sudah memiliki paket berjalan
      if (Auth::user()->hasActiveSubscription()) {
        return redirect()->route('front.index');
      }
  
      DB::transaction(function () use ($request, $user) {

        // validasinya ada di form request tersendiri di StoreCategoryRequest.php
        $data = $request->validated();
  
        if ($request->hasFile('proof')) {
            // ambil pathnya dan simpan dalam folder proofs dan simpan secara public
          $proofPath = $request->file('proof')->store('proofs', 'public');
          $data['proof'] = $proofPath; // gunakan ini agar tidak private (urlnya harus dari public)
        }
  
        $data['user_id'] = $user->id;
        $data['total_ammount'] = 429000;
        $data['is_paid'] = false;
  
        $transaction = SubscribeTransaction::create($data); // create
      });
  
      return redirect()->route('dashboard');
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
