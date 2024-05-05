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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // gunakan contrained karna dia berelasi dengan table lain | onDelete cascade berguna utk jika kita menghapus data teacher maka seluruh data yg berhunungan dengan user_id akan terhapus, ini agar tidak terjadi cacat data. cth: jika guru sudah di hapus maka coursenya juga harus terhapus, klo ga terhapus maka dia cacat data
            $table->boolean('is_active')->default(1); // default 1 yaitu active
            $table->softDeletes(); // deleted_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
