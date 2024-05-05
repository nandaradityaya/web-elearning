<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'about',
        'path_trailer',
        'thumbnail',
        'teacher_id',
        'category_id',
    ];

    public function category() {
        return $this->belongsTo(Category::class); // Courses di miliki oleh Category / category memiliki banyak course (category dan courses berelasigory::class); // Courses di miliki oleh Category / category memiliki banyak course (category dan courses berelasi) 
    }

    public function teacher() {
        return $this->belongsTo(Teacher::class); 
    }

    public function course_videos() {
        return $this->hasMany(CourseVideo::class); 
    }

    public function course_keypoints() {
        return $this->belongsTo(CourseKeypoint::class); 
    }

    // konsep many to many sehingga harus memiliki pivot table atau table jembatan antara users -> course_students -> courses
    public function students() {
        return $this->belongsToMany(User::class, 'course_students'); // course memiliki data pengguna, namun datanya di simpan di dalam table course_students. jadi tidak langsung ke table users | ini di karenakan many to many jadi harus ada jembatan table baru untuk menghubungkan antara users dan courses
    }
}
