<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseVideoController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscribeTransactionController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

// route untuk pengunjung biasa yg ingin melihat2 web (tanpa harus login)
Route::get('/', [FrontController::class, 'index'])->name('front.index'); // function index yg ada di FrontController
Route::get('/details/{course:slug}', [FrontController::class, 'details'])->name('front.details'); // gunakan slug agar halaman details dinamis, pada controller juga harus menerima parameter Course $course (model binding)
Route::get('/category/{category:slug}', [FrontController::class, 'category'])->name('front.category');
Route::get('/pricing', [FrontController::class, 'pricing'])->name('front.pricing');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // harus login sebelum create transaction
    Route::get('/checkout', [FrontController::class, 'checkout'])->name('front.checkout')->middleware('role:student'); // get utk melihat detail harga -> berikan middleware role:student agar yg bisa checkout hanya student
    Route::post('/checkout/store', [FrontController::class, 'checkout_store'])->name('front.checkout.store')->middleware('role:student');; // post untuk submit data bukti pembayaran -> berikan middleware role:student agar yg bisa checkout hanya student

    // domain.com/learning/100/5 = belajar React JS
    Route::post('/learning/{course}/{courseVideoId}', [FrontController::class, 'learning'])->name('front.learning')->middleware('role:student|teacher|owner');

    Route::prefix('admin')->name('admin.')->group(function () {
        // resource itu berarti sudah ada semua, tidak perlu get, patch, delete, dll. semua sudah ada di controller. cth: admin.categories.index
        // rolenya sudah di atur oleh laravel spatie
        Route::resource('categories', CategoryController::class)->middleware('role:owner');
        Route::resource('teachers', TeacherController::class)->middleware('role:owner');
        Route::resource('courses', CourseController::class)->middleware('role:owner|teacher'); // role yg bisa akses adalah owner dan teacher
        Route::resource('subscribe_transactions', SubscribeTransactionController::class)->middleware('role:owner'); 

        // untuk memposting kelas maka harus menggunakan get dan post karna kita harus menyimpan course id. tidak bisa menggunakan resource (namun untuk delete dan edit bisa menggunakan resource yg ada di bawah)
        Route::get('add/video/{course:id}', [CourseVideoController::class, 'create'])
        ->middleware('role:teacher|owner')
        ->name('course.add_video');

        Route::post('add/video/save/{course:id}', [CourseVideoController::class, 'store'])
        ->middleware('role:teacher|owner')
        ->name('course.add_video.save');

        Route::resource('course_videos', CourseVideoController::class)->middleware('role:owner|teacher'); 


    });
});

require __DIR__.'/auth.php';
