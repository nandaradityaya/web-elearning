<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $categories = Category::orderByDesc('id')->get(); // ambil semua model category
        // dd($categories);

        return view('admin.categories.index', compact('categories')); // compact utk lempar data categories yg ingin ditampilkan di views
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        //
        

        DB::transaction(function () use ($request){

            // validasinya ada di form request tersendiri di StoreCategoryRequest.php
            $validated = $request->validated();

            if($request->hasFile('icon')) {
                // ambil pathnya dan simpan dalam folder icons dan simpan secara public
                $iconPath = $request->file('icon')->store('icons', 'public'); 
                $validated['icon'] = $iconPath; // gunakan ini agar tidak private (urlnya harus dari public)
            } else {
                $iconPath = 'images/icon-default.png'; // default image jika tdk ada image dr user
            }

            $validated['slug'] = Str::slug($validated['name']);
            // gunakan slug agar urlnya dari web design menjadi web-design

            $category = Category::create($validated); // create data terbaru dengan name, icon, dan slug
        });

        return redirect()->route('admin.categories.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        //
    }
}
