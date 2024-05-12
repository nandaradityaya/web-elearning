<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index () {
        return view ('front.index'); // tampilkan view index ada di folder views/front/index.blade.php
    }

    // menerima parameter course karna Course memiliki slug yg berisi link details course (ini dinamakan model binding)
    public function details (Course $course) {
        return view ('front.details'); 
    }
}
