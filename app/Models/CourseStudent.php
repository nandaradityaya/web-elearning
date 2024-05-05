<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseStudent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'course_id',
    ];

    // CourseStudent merupakan pivot table (table penghubung) jadi ga perlu di atur relasinya karna sudah diatur di Users dan Courses
}
