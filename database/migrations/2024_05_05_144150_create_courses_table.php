<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('path_trailer');
            $table->text('about');
            $table->string('thumbnail');
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade'); // gunakan contrained karna dia berelasi dengan table lain | onDelete cascade berguna utk jika kita menghapus data teacher maka seluruh data yg berhunungan dengan user_id akan terhapus, ini agar tidak terjadi cacat data.
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->softDeletes(); // deleted_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
