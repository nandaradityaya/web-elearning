<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    // cara pertama dalam persiapan mass assignment
    protected $fillable = [
        'name',
        'slug',
        'icon',
    ];

    // cara kedua | menggunakan guarded yg berarti field yg ada di dalam guarded tidak bisa di isi oleh user dan field yg lain boleh
    // user dapat memasukkan data apa saja yang membahayakan sistem
    protected $guarded = [
        'id',
    ];

    public function courses() {
        return $this->hasMany(Course::class); // category memiliki banyak courses
    }
}
